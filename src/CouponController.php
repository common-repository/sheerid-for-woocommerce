<?php

namespace WooCommerce\SheerID;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Options\CouponOptions;
use WooCommerce\SheerID\Utils\JWTUtil;

class CouponController {

	const SHEERID_COUPON_E_INVALID_PERMISSION = - 8400;

	const  SHEERID_COUPON_E_NOT_FOUND = - 8401;

	const SHEERID_COUPON_E_INCOMPLETE_VERIFICATION = - 8402;

	const SHEERID_COUPON_E_DOC_UPLOAD = - 8403;

	const SHEERID_COUPON_E_PENDING = - 8404;

	private $client;

	private $context;

	public function __construct( BaseClient $client, ContextDataController $context ) {
		$this->client  = $client;
		$this->context = $context;
	}

	public function initialize() {
		add_action( 'woocommerce_applied_coupon', [ $this, 'validate_coupon' ], 10 );
		add_filter( 'woocommerce_coupon_error', [ $this, 'get_coupon_message' ], 10, 3 );
	}

	public function validate_coupon( $code ) {
		$cart    = WC()->cart;
		$coupon  = new \WC_Coupon( $code );
		$options = new CouponOptions( $coupon );

		if ( $options->has_program() ) {
			$program = sheerid_wc_get_program( [ 'program_id' => $options->get_program_id() ] );
			if ( $program ) {
				try {
					// make sure the user has a valid verification for this program
					$user_id = WC()->session->get_customer_id();

					$verification = sheerid_wc_get_verification( [
						'user_id' => $user_id,
						'program' => $program->get_program_id(),
						'mode'    => $program->get_mode()
					] );

					if ( $verification ) {
						if ( ! $verification->is_valid() ) {
							throw new \Exception( 'invalid', self::SHEERID_COUPON_E_INVALID_PERMISSION );
						}
						$response = $this->client->verificationDetails->retrieve( $verification->get_verification_id() );

						// verify that the verification is not a year old
						if ( is_wp_error( $response ) ) {
							throw new \Exception( 'invalid', self::SHEERID_COUPON_E_INVALID_PERMISSION );
						} elseif ( ! $verification->is_success() ) {
							$status = $verification->get_status();
							// @todo - might need code here that changes messaging based on the status of the verification.
							// For example, if the verification requires a document upload, the customer should be informed.
							if ( $status === 'docUpload' ) {
								throw new \Exception( 'invalid', self::SHEERID_COUPON_E_DOC_UPLOAD );
							} elseif ( $status === 'pending' ) {
								throw new \Exception( 'invalid', self::SHEERID_COUPON_E_PENDING );
							}
							throw new \Exception( 'invalid', self::SHEERID_COUPON_E_INCOMPLETE_VERIFICATION );
						}
					} else {
						throw new \Exception( 'invalid', self::SHEERID_COUPON_E_INVALID_PERMISSION );
					}
				} catch ( \Exception $e ) {
					\wc_clear_notices();
					$coupon->add_coupon_message( $e->getCode() );
					// remove the coupon from the cart
					$this->remove_coupon_code( $coupon, $cart );
				}
			}
		}
	}

	/**
	 * @param            $msg
	 * @param            $msg_code
	 * @param \WC_Coupon $coupon
	 *
	 * @return mixed|string
	 */
	public function get_coupon_message( $msg, $msg_code, $coupon ) {
		$link_msg = '';
		switch ( $msg_code ) {
			case self::SHEERID_COUPON_E_INVALID_PERMISSION:
			case self::SHEERID_COUPON_E_INCOMPLETE_VERIFICATION:
				$msg      = sprintf( __( 'Please verify your identity before adding coupon %1$s to your cart.', 'sheerid-for-woocommerce' ), $coupon->get_code() );
				$link_msg = sprintf( __( ' Click %1$shere%2$s to verify your identity.', 'sheerid-for-woocommerce' ), ...$this->get_verification_link_args( $coupon ) );
				break;
			case self::SHEERID_COUPON_E_DOC_UPLOAD:
				$msg      = sprintf( __( 'You will need to complete your identity verification to use coupon %1$s.' ), $coupon->get_code() );
				$link_msg = sprintf( __( ' Please click %1$shere%2$s to complete your identity verification.', 'sheerid-for-woocommerce' ), ...$this->get_verification_link_args( $coupon ) );
				break;
			case self::SHEERID_COUPON_E_PENDING:
				$msg = __( 'Your verification documents are currently being reviewed. This process usually takes a few minutes. We will email you when the review is complete.', 'sheerid-for-woocommerce' );
				break;
		}
		if ( $this->is_verification_link_enabled( $coupon ) ) {
			$msg .= $link_msg;
		}

		/*if ( $this->is_verification_link_enabled( $coupon ) ) {
			$msg .= sprintf( __( ' Please click %1$shere%2$s to complete your identity verification.', 'sheerid-for-woocommerce' ), '<a href=" ' . $url . '">', '</a>' );
		}*/

		return $msg;
	}

	/**
	 * @param \WC_Coupon $coupon
	 * @param \WC_Cart   $cart
	 *
	 * @return void
	 */
	private function remove_coupon_code( $coupon, $cart ) {
		$key = \array_search( $coupon->get_code(), $cart->applied_coupons );
		if ( $key !== false ) {
			unset( $cart->applied_coupons[ $key ] );
		}
	}

	/**
	 * @param \WC_Coupon $coupon
	 *
	 * @return void
	 */
	private function is_verification_link_enabled( $coupon ) {
		return $coupon->get_meta( Constants::SHEERID_ENABLE_LINK );
	}

	/**
	 * @param \WC_Coupon $coupon
	 *
	 * @return string
	 */
	private function get_verification_link_args( $coupon ) {
		$options = new CouponOptions( $coupon );
		$this->context->set_coupon( $coupon );

		return [ '<a ' . $options->get_element_attributes() . '>', '</a>' ];
	}

}