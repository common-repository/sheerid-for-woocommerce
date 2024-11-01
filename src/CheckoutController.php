<?php

namespace WooCommerce\SheerID;

use Automattic\WooCommerce\StoreApi\Exceptions\InvalidCartException;
use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\Settings\CheckoutSettings;
use WooCommerce\SheerID\Assets\AssetData;
use WooCommerce\SheerID\Blocks\ExtendAPI\ExtendCart;
use WooCommerce\SheerID\Utils\JavascriptData;

class CheckoutController {

	private $client;

	private $settings;

	private $asset_data;

	public function __construct( BaseClient $client, CheckoutSettings $settings, AssetData $asset_data ) {
		$this->client     = $client;
		$this->settings   = $settings;
		$this->asset_data = $asset_data;
	}

	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_print_scripts', [ $this, 'print_scripts' ], 2 );
		add_action( 'woocommerce_checkout_order_processed', [ $this, 'validate_legacy_checkout' ], 10, 3 );
		add_action( 'woocommerce_store_api_checkout_order_processed', [ $this, 'validate_api_checkout' ], 10 );
	}

	private function is_validation_enabled() {
		return apply_filters( 'woocommerce_sheerid_checkout_validation_enabled', $this->settings->is_validation_enabled() );
	}

	private function is_order_validation_enabled( $order ) {
		return apply_filters( 'woocommerce_sheerid_order_validation_enabled', $this->settings->is_validation_enabled(), $order );
	}

	public function enqueue_scripts() {
		if ( $this->settings->is_validation_enabled() ) {
			if ( is_checkout() ) {
				wp_enqueue_script( 'wc-sheerid-checkout-verification' );
				wp_enqueue_style( Constants::SHEER_ID_STYLES );
			}
		}
	}

	public function print_scripts() {
		if ( ! is_admin() && $this->settings->is_validation_enabled() && is_checkout() ) {
			//$messages = sheerid_wc_container()->get( MessageMap::class );

			$program = sheerid_wc_get_program( [ 'program_id' => $this->settings->get_option( 'program' ) ] );

			if ( $program ) {
				$verification = sheerid_wc_get_verification( [ 'program' => $program->get_program_id(), 'user_id' => WC()->session->get_customer_id() ] );

				//$this->asset_data->add( 'messages', $messages->get_contextual_messages( $verification, [ 'context' => 'checkout' ] ) );
				$this->asset_data->add( 'program', $program->get_program_id() );
				$this->asset_data->add( 'page_id', wc_get_page_id( 'checkout' ) );
				$this->asset_data->add( 'context_args', [ 'context' => 'checkout' ] );
				$this->asset_data->add( 'config', [
					'onPageLoad'        => $this->is_validation_enabled() && $this->settings->get_option( 'type' ) === 'page_load',
					'needsVerification' => ! $verification || ! $verification->is_success() || ! $verification->is_valid()
				] );
				$this->asset_data->prepare_script_data( 'wc-sheerid-checkout-verification' );
			}
		}
	}

	/**
	 * @param int       $order_id
	 * @param array     $posted_data
	 * @param \WC_Order $order
	 *
	 * @return void
	 */
	public function validate_legacy_checkout( $order_id, $posted_data, $order ) {
		$verification = $this->validate_checkout( $order );

		if ( $verification && ! $verification->is_success() ) {
			return \wp_send_json( [
				'result'   => 'success',
				'redirect' => '#sheerid-response=' . rawurlencode( base64_encode( wp_json_encode( $this->get_verification_response( $verification, $order ) ) ) ) //phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
			], 200 );
		}
	}

	/**
	 * @param \WP_Error $errors
	 * @param \WC_Cart  $cart
	 *
	 * @return void
	 */
	public function validate_api_checkout( $order ) {
		$verification = $this->validate_checkout( $order );
		if ( $verification && ! $verification->is_success() ) {
			wc_sheerid_container()->get( ExtendCart::class )->set_data( $this->get_verification_response( $verification, $order ) );
			throw new InvalidCartException(
				'sheerid_invalid_verification',
				new \WP_Error( 'sheerid_invalid_verification', __( 'Verification is required to proceed.', 'sheerid-for-woocommerce' ) ),
				409
			);
		}
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return void
	 */
	private function validate_checkout( $order ) {
		if ( $this->is_order_validation_enabled( $order ) ) {
			$user_id = $order->get_customer_id();
			if ( ! $user_id ) {
				$user_id = WC()->session->get_customer_id();
			}

			$program = sheerid_wc_get_program( [ 'program_id' => $this->settings->get_option( 'program' ) ] );

			if ( $program ) {
				$verification = sheerid_wc_get_verification( [
					'user_id'    => $user_id,
					'program_id' => $program->get_program_id(),
					'mode'       => $program->get_mode()
				] );

				// If a verification doesn't exist or it has an error state, create a new one.
				if ( ! $verification || $verification->is_error() ) {
					$response = $this->client->verifications->create( [
						'programId' => $program->get_program_id(),
						'metadata'  => [
							'session_id'  => (string) WC()->session->get_customer_id(),
							'customer_id' => WC()->customer ? (string) WC()->customer->get_id() : '0',
							'origin_url'  => wc_get_page_id( 'checkout' ),
							'live'        => $program->is_live()
						]
					] );

					if ( \is_wp_error( $response ) ) {
						throw new \Exception( $response->get_error_message() ); //phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
					} else {
						$verification = new Verification();
						$verification->set_program_id( $program->get_program_id() );
						$verification->set_user_id( WC()->session->get_customer_id() );
						$verification->set_verification_id( $response->getVerificationId() );
						$verification->set_status( $response->getCurrentStep() );
						$verification->set_mode( $program->is_live() ? 'live' : 'test' );
						$verification->set_segment( $response->getSegment() );
						$verification->save();
					}
				} elseif ( ! $verification->is_success() ) {
					$verification_details = $this->client->verificationDetails->retrieve( $verification->get_verification_id() );
					if ( ! is_wp_error( $verification_details ) ) {
						$verification->update_status( $verification_details->getLastResponse()->getCurrentStep() );
					}
				}

				return $verification;
			}
		}
	}

	/**
	 * @param \WooCommerce\SheerID\Verification $verification
	 * @param \WC_Order                         $order
	 *
	 * @return mixed|null
	 */
	private function get_verification_response( $verification, \WC_Order $order ) {
		return JavascriptData::get_verification_data( [
			'page_id'      => wc_get_page_id( 'checkout' ),
			'program'      => $this->settings->get_option( 'program' ),
			'verification' => $verification->get_verification_id(),
			'messages'     => wc_sheerid_container()->get( MessageMap::class )->get_contextual_messages( $verification, [ 'context' => 'checkout' ] ),
			'view_model'   => array_filter( [
				'firstName' => $order->get_billing_first_name(),
				'lastName'  => $order->get_billing_last_name(),
				'email'     => $order->get_billing_email()
			] ),
			'url'          => add_query_arg( [ 'verificationId' => $verification->get_verification_id() ], sprintf( Constants::VERIFICATION_URL, $verification->get_program_id() ) ),
			'time'         => time(),
			'context_args' => [
				'context' => 'checkout'
			]
		] );
	}

}