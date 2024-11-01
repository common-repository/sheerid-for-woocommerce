<?php

namespace WooCommerce\SheerID\Admin;

use SheerID\Client\BaseClient;
use SheerID\Model\Webhook;
use WooCommerce\SheerID\Admin\Settings\APISettings;
use WooCommerce\SheerID\Database\ProgramDataStore;
use WooCommerce\SheerID\Emails\VerificationReminderEmail;
use WooCommerce\SheerID\Logger;
use WooCommerce\SheerID\Program;

class AdminAjaxController {

	private $client;

	private $log;

	public function __construct( BaseClient $client, Logger $log ) {
		$this->client = $client;
		$this->log    = $log;
	}

	public function initialize() {
		foreach ( $this->get_ajax_mappings() as $k => $v ) {
			if ( \is_callable( $v ) ) {
				add_action( "wp_ajax_{$k}", $this->get_callback_handler( $v ) );
			}
		}
	}

	private function get_callback_handler( $callback ) {
		return function () use ( $callback ) {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				$status = 200;
				$action = isset( $_GET['action'] ) ? \sanitize_text_field( \wp_unslash( $_GET['action'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				try {
					$data    = \WP_REST_Server::get_raw_data();
					$request = new \WP_REST_Request( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$request->set_body( $data );
					$request->set_headers( [
						'CONTENT_TYPE' => ! empty( $_SERVER['CONTENT_TYPE'] ) ? \sanitize_text_field( \wp_unslash( $_SERVER['CONTENT_TYPE'] ) ) : 'application/json'
					] );
					if ( isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) {
						$request->set_method( \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) );
					}
					$result = call_user_func( $callback, $request );
				} catch ( \Exception $e ) {
					$result = new \WP_Error( 'sheerid_error', $e->getMessage() );
				}
				if ( \is_wp_error( $result ) ) {
					$result = [
						'code'    => $result->get_error_code(),
						'message' => $result->get_error_message(),
						'data'    => $result->get_error_data()
					];
				}
			} else {
				$result = [
					'code'    => 'unauthorized',
					'message' => __( 'You are not authorized to access this resource.', 'sheerid-for-woocommerce' )
				];
				$status = 403;
			}

			return wp_send_json( apply_filters( 'wc_sheerid_admin_request_' . $action, $result, $status ), $status );
		};
	}

	private function get_ajax_mappings() {
		return [
			'fetch_segments'             => [ $this, 'fetch_segments' ],
			'create_program'             => [ $this, 'create_program' ],
			'fetch_program'              => [ $this, 'fetch_program' ],
			'update_program'             => [ $this, 'update_program' ],
			'delete_program'             => [ $this, 'delete_program' ],
			'delete_verification'        => [ $this, 'delete_verification' ],
			'toggle_program_mode'        => [ $this, 'toggle_program_mode' ],
			'connect_plugin'             => [ $this, 'connect_plugin' ],
			'sync_programs'              => [ $this, 'sync_programs' ],
			'send_verification_reminder' => [ $this, 'send_verification_reminder' ],
			'create_webhook'             => [ $this, 'create_webhook' ],
			'delete_webhook'             => [ $this, 'delete_webhook' ]
		];
	}

	public function fetch_segments() {
		$segments = $this->client->segments->all();
		if ( ! is_wp_error( $segments ) ) {
			return [ 'segments' => $segments ];
		}

		return $segments;
	}

	public function fetch_program( \WP_REST_Request $request ) {
		$program = $this->client->programs->retrieve( $request->get_param( 'program' ) );
		if ( is_wp_error( $program ) ) {
			throw new \Exception( $program->get_error_message() ); //phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
		$webhooks = $this->client->webhooks->all( $program->getId() );

		if ( ! is_wp_error( $webhooks ) ) {
			$webhooks = $this->filter_webhooks( $webhooks->items() );
		} else {
			$webhooks = [];
		}

		$local_program = sheerid_wc_get_program( [ 'program_id' => $request->get_param( 'program' ) ] );

		if ( ! $local_program ) {
			$local_program = new Program();
			$local_program->set_program_id( $program->getId() );
			$local_program->set_mode( $program->isLive() ? 'live' : 'test' );
			$local_program->save();
		}

		return [
			'program' =>
				array_merge(
					$program->jsonSerialize(),
					[
						'webhook_domain' => $local_program->get_webhook_domain(),
						'webhook_scheme' => $local_program->get_webhook_scheme(),
						'webhooks'       => $webhooks,
						'emails'         => [
							'success'  => $local_program->is_success_email_enabled(),
							'failure'  => $local_program->is_failure_email_enabled(),
							'reminder' => $local_program->is_reminder_email_enabled()
						]
					]
				)
		];
	}

	public function create_program( \WP_REST_Request $request ) {
		$name = $request->get_param( 'segmentName' );
		if ( ! $name ) {
			throw new \Exception( esc_html__( 'Segment name is a required field.', 'sheerid-for-woocommerce' ) );
		}
		$program  = $this->client->programs->create( [ 'segmentDescriptionName' => $name ] );
		$response = [];
		if ( ! is_wp_error( $program ) ) {
			$local_program = new Program();
			$local_program->set_program_id( $program->getId() );
			$local_program->set_mode( $program->isLive() ? 'live' : 'test' );
			$local_program->save();

			$response = [
				'program' => array_merge( $program->jsonSerialize(), [
					'emails' => [
						'success'  => $local_program->is_success_email_enabled(),
						'failure'  => $local_program->is_failure_email_enabled(),
						'reminder' => $local_program->is_reminder_email_enabled()
					]
				] )
			];

			// setup the webhooks for the program
			$webhooks = $this->create_webhooks( $program->getId() );
			if ( is_wp_error( $webhooks ) ) {
				$webhooks = [];
				$this->log->error( sprintf( 'Error generating webhooks during program creation. Error: %1$s', $webhooks->get_error_message() ) );
			} else {
				$webhooks = $this->filter_webhooks( $webhooks->items() );
				$local_program->save();
			}
			$response['program']['webhooks'] = $webhooks;
		}

		return $response;
	}

	public function update_program( \WP_REST_Request $request ) {
		$program = $this->client->programs->retrieve( wc_clean( $request->get_param( 'program_id' ) ) );
		if ( is_wp_error( $program ) ) {
			throw new \Exception( esc_html__( 'Invalid program ID.', 'sheerid-for-woocommerce' ) );
		}

		if ( ! empty( $request['audience'] ) ) {
			// update the age program
			$audience = $request->get_param( 'audience' );
			$audience = wp_parse_args( $audience, $program->getAudience()->jsonSerialize() );
			$this->client->programs->updateAudience( $program->getId(), $audience );
		}

		// Update the program name
		if ( $program->getName() !== $request->get_param( 'name' ) ) {
			$this->client->programs->updateName( $program->getId(), \wc_clean( $request->get_param( 'name' ) ) );
		}

		$local_program = sheerid_wc_get_program( [ 'program_id' => $program->getId() ] );
		/**
		 *
		 */
		if ( $local_program ) {
			$emails = \wc_clean( $request->get_param( 'emails' ) );
			if ( $emails ) {
				$emails = wp_parse_args( $emails, [
					'success'  => true,
					'failure'  => true,
					'reminder' => true
				] );
				$local_program->set_success_email( $emails['success'] );
				$local_program->set_failure_email( $emails['failure'] );
				$local_program->set_reminder_email( $emails['reminder'] );
			}

			$local_program->save();
		}

		return [
			'message' => __( 'Your program has been updated.', 'sheerid-for-woocommerce' )
		];
	}

	public function delete_program( \WP_REST_Request $request ) {
		$program = \wc_clean( $request->get_param( 'program' ) );
		if ( ! $program ) {
			throw new \Exception( esc_html__( 'Program ID is a required field.', 'sheerid-for-woocommerce' ) );
		}
		$response = $this->client->programs->archive( $program );
		if ( ! is_wp_error( $response ) ) {
			return [ 'program' => $response ];
		}

		return [];
	}

	public function delete_verification( \WP_REST_Request $request ) {
		$verification = \wc_clean( $request->get_param( 'verification' ) );
		if ( ! $verification ) {
			throw new \Exception( esc_html__( 'Invalid verification ID.', 'sheerid-for-woocommerce' ) );
		}
		$verification = \sheerid_wc_get_verification( $verification );
		$verification->delete();

		return [];
	}

	public function toggle_program_mode( \WP_REST_Request $request ) {
		$program_id = \wc_clean( $request->get_param( 'program_id' ) );
		if ( ! $program_id ) {
			throw new \Exception( esc_html__( 'Program ID is a required field.', 'sheerid-for-woocommerce' ) );
		}
		$program = $this->client->programs->retrieve( $program_id );
		if ( ! is_wp_error( $program ) ) {
			$local_program = sheerid_wc_get_program( [ 'program_id' => $program_id ] );
			if ( $program->isLive() ) {
				$program = $this->client->programs->unlaunch( $program_id );
			} else {
				$program = $this->client->programs->launch( $program_id );
			}

			if ( $local_program && ! is_wp_error( $program ) ) {
				$local_program->set_mode( $program->isLive() ? 'live' : 'test' );
				$local_program->save();
			}
		}

		return [];
	}

	public function sync_programs( \WP_REST_Request $request ) {
		$programs       = $this->client->programs->all();
		$ids            = array_map( function ( $program ) {
			return $program->getId();
		}, $programs->items() );
		$local_programs = sheerid_wc_get_programs( [ 'program_ids' => $ids ] );

		$local_programs = \array_reduce( $local_programs, function ( $carry, $program ) {
			$carry[ $program->get_program_id() ] = $program;

			return $carry;
		}, [] );

		foreach ( $programs->items() as $program ) {
			if ( isset( $local_programs[ $program->getId() ] ) ) {
				$local_program = $local_programs[ $program->getId() ];
			} else {
				$local_program = new Program();
			}
			$local_program->set_program_id( $program->getId() );
			$local_program->set_mode( $program->isLive() ? 'live' : 'test' );
			$local_program->save();
		}

		return [];
	}

	public function connect_plugin( \WP_REST_Request $request ) {
		$username = \wc_clean( $request->get_param( 'username' ) );
		$password = \wc_clean( $request->get_param( 'password' ) );
		$shop_id  = md5( site_url() );

		// fetch the secret key from SheerID
		$response = $this->client->login->connect( [
			'username'    => $username,
			'password'    => $password,
			'override'    => true,
			'wooCommerce' => [
				'shopId' => $shop_id
			]
		] );

		if ( ! is_wp_error( $response ) ) {
			// save the bearer token
			/**
			 * @var APISettings $settings
			 */
			$settings = sheerid_wc_container()->get( APISettings::class );
			$settings->update_option( 'access_token', $response->getBearerToken() );

			ob_start();
			$settings->admin_options();
			$html = ob_get_clean();

			return [
				'message' => __( 'Your WooCommerce store has been connected to your SheerID account. You can now process verifications.', 'sheerid-for-woocommerce' ),
				'html'    => $html
			];
		} else {
			return $response;
		}
	}

	public function create_webhook( \WP_REST_Request $request ) {
		$program_id = $request->get_param( 'program_id' );

		$response = $this->create_webhooks( $program_id );

		$webhooks = $this->client->webhooks->all( $program_id );

		if ( ! is_wp_error( $webhooks ) ) {
			$webhooks = $this->filter_webhooks( $webhooks->items() );
		} else {
			$webhooks = [];
		}

		return [
			'webhooks' => $webhooks
		];
	}

	public function delete_webhook( \WP_REST_Request $request ) {
		$ids        = $request->get_param( 'ids' );
		$program_id = $request->get_param( 'program_id' );

		foreach ( $ids as $id ) {
			$this->client->webhooks->delete( $program_id, $id );
		}

		$webhooks = $this->client->webhooks->all( $program_id );
		if ( ! is_wp_error( $webhooks ) ) {
			$webhooks = $this->filter_webhooks( $webhooks->items() );
		} else {
			$webhooks = [];
		}

		return [
			'message'  => __( 'Webhook as been deleted.', 'sheerid-for-woocommerce' ),
			'webhooks' => $webhooks
		];
	}

	private function create_webhooks( $program_id, $url = '' ) {
		if ( empty( $url ) ) {
			$url = get_rest_url( null, '/wc-sheerid/v1/webhook' );
		}

		// setup the webhooks for the program
		return $this->client->webhooks->create( $program_id, [
			[
				'type'        => Webhook::SUCCESS,
				'callbackUri' => $url
			],
			[
				'type'        => Webhook::FAILURE,
				'callbackUri' => $url
			],
			[
				'type'        => Webhook::PROGRAM_CHANGE,
				'callbackUri' => $url
			],
			[
				'type'        => Webhook::REMINDER,
				'callbackUri' => $url
			],
			[
				'type'        => Webhook::EMAIL_LOOP,
				'callbackUri' => $url
			],
			[
				'type'        => Webhook::NEED_MORE_DOCS,
				'callbackUri' => $url
			]
		] );
	}

	public function send_verification_reminder( \WP_REST_Request $request ) {
		$verification = \wc_clean( $request->get_param( 'verification' ) );
		$email        = \wc_clean( $request->get_param( 'email' ) );
		if ( ! $verification ) {
			throw new \Exception( esc_html__( 'Invalid verification ID.', 'sheerid-for-woocommerce' ) );
		}
		$verification = \sheerid_wc_get_verification( $verification );
		// send the reminder email

		// make sure emails are initialized
		WC()->mailer();

		/**
		 * @var VerificationReminderEmail $email_instance
		 */
		$email_instance            = sheerid_wc_container()->get( VerificationReminderEmail::class );
		$email_instance->recipient = $email;
		$email_instance->trigger( $verification );

		if ( $email_instance->last_error ) {
			return [
				'success' => false,
				'message' => $email_instance->last_error
			];
		}

		return [
			'success' => true,
			'message' => sprintf( __( 'Reminder notification has been delivered to %s', 'sheerid-for-woocommerce' ), $email )
		];
	}

	private function filter_webhooks( $webhooks = [] ) {
		return array_values( array_reduce( $webhooks, function ( $carry, Webhook $item ) {
			if ( ! isset( $carry[ $item->getCallbackUri() ] ) ) {
				$carry[ $item->getCallbackUri() ] = [
					'callBackUri' => $item->getCallbackUri(),
					'events'      => [ $item->getType() ],
					'ids'         => [ $item->getId() ]
				];
			} else {
				$carry[ $item->getCallbackUri() ]['events'][] = $item->getType();
				$carry[ $item->getCallbackUri() ]['ids'][]    = $item->getId();
			}

			return $carry;
		}, [] ) );
	}

}