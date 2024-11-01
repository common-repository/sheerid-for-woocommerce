<?php

namespace WooCommerce\SheerID\Rest\Route;

use SheerID\Model\Webhook;
use WooCommerce\SheerID\Logger;
use WooCommerce\SheerID\Program;
use WooCommerce\SheerID\SheerIDClient;

class WebhookRoute extends AbstractRoute {

	const HEADER = 'X_SHEERID_WEBHOOK_TYPE';

	private $client;

	private $log;

	public function __construct( SheerIDClient $client, Logger $log ) {
		$this->client = $client;
		$this->log    = $log;
	}

	protected $path = '/webhook';

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ]
			]
		];
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		try {
			if ( ! $request->get_header( self::HEADER ) ) {
				throw new \Exception( __( 'Missing required webhook type header.', 'sheerid-for-woocommerce' ), 400 );
			}

			$type = $request->get_header( self::HEADER );

			$this->log->info( sprintf( 'Processing event type: %s', $type ) );

			$result = [ 'success' => true ];

			$args = [];

			switch ( $type ) {
				case Webhook::TEST:
					break;
				case Webhook::SUCCESS:
				case Webhook::FAILURE:
				case Webhook::REMINDER:
				case Webhook::NEED_MORE_DOCS:
					$verification_id = $request->get_param( 'verificationId' );
					if ( ! $verification_id ) {
						throw new \Exception( __( 'Missing or invalid verification ID.', 'sheerid-for-woocommerce' ), 400 );
					}
					$verification_id = $request->get_param( 'verificationId' );

					// use the verification ID to fetch the verification status
					$verification_details = $this->client->verificationDetails->retrieve( $verification_id );

					if ( ! is_wp_error( $verification_details ) ) {
						// get the verification from the db
						$verification = sheerid_wc_get_verification( [ 'verification' => $verification_id ] );

						if ( $verification ) {
							$args[] = $verification;
							if ( $verification_details->getPersonInfo() ) {
								$verification->set_first_name( $verification_details->getPersonInfo()->getFirstName() );
								$verification->set_last_name( $verification_details->getPersonInfo()->getLastName() );
								$verification->set_email( $verification_details->getPersonInfo()->getEmail() );
								$verification->save();
							}

							$verification->update_status( $verification_details->getLastResponse()->getCurrentStep() );

							if ( $type === Webhook::REMINDER ) {
								// This webhook is sent by SheerID approximately 15 minutes after a consumer initiates a verification
								// and they haven't completed it.
								do_action( 'woocommerce_sheerid_verification_reminder', $verification->get_id(), $verification );
							} elseif ( $type === Webhook::NEED_MORE_DOCS ) {
								if ( $verification_details->getLastResponse()->getRejectionReasons() ) {
									// This webhook is triggered when a docUpload fails and SheerID requires more documentation. The verification
									// will have a docUpload state, not an error state. So trigger this action so emails with the document rejection reason
									// can be sent
									do_action( 'woocommerce_sheerid_verification_upload_failed', $verification->get_id(), $verification, $verification_details );
								}
							}
						}
					} else {
						throw new \Exception( __( 'Missing or invalid verification ID.', 'sheerid-for-woocommerce' ), 404 );
					}
					break;
				case Webhook::PROGRAM_CHANGE:
					// update the program so it's in sync with the SheerID program
					$program_id = $request->get_param( 'program_id' );
					if ( ! $program_id ) {
						throw new \Exception( __( 'Program ID is a required field.', 'sheerid-for-woocommerce' ), 400 );
					}
					$program = $this->client->programs->retrieve( $program_id );
					if ( is_wp_error( $program ) ) {
						throw new \Exception( __( 'Invalid program ID.', 'sheerid-for-woocommerce' ), 404 );
					}
					$local_program = sheerid_wc_get_program( [ 'program_id' => $program->getId() ] );
					if ( ! $local_program ) {
						$local_program = new Program();
						$local_program->set_program_id( $program->getId() );
					}
					$local_program->set_mode( $program->isLive() ? 'live' : 'test' );
					$local_program->save();
					break;
				default:
					throw new \Exception( \sprintf( __( 'Invalid webhook type %s', 'sheerid-for-woocommerce' ), $type ), 400 );
			}
			/**
			 * Allow 3rd party plugins to perform actions based on the webhook event type
			 */
			do_action( 'woocommerce_sheerid_webhook_event_' . strtolower( $type ), $request, ...$args );

			return $result;
		} catch ( \Exception $e ) {
			$this->log->error( sprintf( 'Error processing webhook. Reason: %s', $e->getMessage() ) );
			throw new \Exception( esc_html( $e->getMessage() ), esc_html( $e->getCode() ) );
		}
	}

}