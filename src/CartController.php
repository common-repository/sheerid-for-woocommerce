<?php

namespace WooCommerce\SheerID;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Options\CategoryOptions;
use WooCommerce\SheerID\Options\ProductOptions;
use WooCommerce\SheerID\Utils\GeneralUtils;
use WooCommerce\SheerID\Utils\JavascriptData;

class CartController {

	private $client;

	private $assets;

	public function __construct( BaseClient $client, AssetsApi $assets ) {
		$this->client = $client;
		$this->assets = $assets;
	}

	public function initialize() {
		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'validate_add_to_cart' ], 10, 4 );
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'validate_checkout' ], 10, 2 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		if ( is_product() ) {
			$product  = GeneralUtils::get_queried_product_id();
			$settings = new ProductOptions( $product );

			if ( $settings->has_program() || ( $settings->has_category_settings() && $settings->get_category_settings()->has_program() ) ) {
				wp_enqueue_script( 'wc-sheerid-product-verification' );
				wp_enqueue_style( Constants::SHEER_ID_STYLES );
				wp_enqueue_style( 'wc-sheerid-frontend-styles' );
			}
		} elseif ( is_checkout() ) {
			/*if ( WC()->cart ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					/**
					 * @var \WC_Product $product
					 */
					/*$product = $values['data'];
					if ( $product ) {
						$settings = new ProductOptions( $product );
						if ( $settings->has_program() || $settings->has_category_settings() ) {
							wp_enqueue_script( 'wc-sheerid-product-verification' );
							wp_enqueue_style( Constants::SHEER_ID_STYLES );
							wp_enqueue_style( 'wc-sheerid-frontend-styles' );
							break;
						}
					}
				}*/
			/*}*/
			wp_enqueue_script( 'wc-sheerid-product-verification' );
			wp_enqueue_style( Constants::SHEER_ID_STYLES );
			wp_enqueue_style( 'wc-sheerid-frontend-styles' );
		} elseif ( is_cart() ) {
			wp_enqueue_script( 'wc-sheerid-product-verification' );
			wp_enqueue_style( Constants::SHEER_ID_STYLES );
			wp_enqueue_style( 'wc-sheerid-frontend-styles' );
		}
	}

	public function validate_add_to_cart( $valid, $product_id, $quantity, $variation_id = 0 ) {
		if ( $valid ) {
			$product = \wc_get_product( $product_id );
			if ( $product->get_type() === 'variation' ) {
				$product = \wc_get_product( $product->get_parent_id() );
			}
			JavascriptData::$product = $product;
			$settings                = new ProductOptions( $product );
			if ( ! $settings->has_program() && $settings->has_category_settings() ) {
				$settings = $settings->get_category_settings();
			}

			// a program has been configured and the user must be verified before adding the item to the cart
			if ( $settings->has_program() && $settings->is_require_before_cart() ) {
				sheerid_wc_container()->get( ContextDataController::class )->set_product( $product );
				sheerid_wc_container()->get( ContextDataController::class )->set_context( ContextDataController::PRODUCT );

				$program = sheerid_wc_get_program( [ 'program_id' => $settings->get_option( 'program' ) ] );

				if ( $program ) {
					$verification = sheerid_wc_get_verification( [
						'user_id'    => WC()->session->get_customer_id(),
						'program_id' => $program->get_program_id(),
						'mode'       => $program->get_mode()
					] );
					try {
						if ( $verification && $verification->is_valid() ) {
							if ( ! $verification->is_success() ) {
								// grab the verification details object from sheerID. It's possible the webhook hasn't been received yet
								// and so the status of the local verification is not accurate.
								$verification_details = $this->client->verificationDetails->retrieve( $verification->get_verification_id() );
								if ( is_wp_error( $verification_details ) ) {
									throw new \Exception( $verification_details->get_error_message() );
								}

								// Update the verification so it's in sync with SheerID
								if ( $verification_details->getPersonInfo() ) {
									$verification->set_first_name( $verification_details->getPersonInfo()->getFirstName() );
									$verification->set_last_name( $verification_details->getPersonInfo()->getLastName() );
									$verification->set_email( $verification_details->getPersonInfo()->getEmail() );
								}

								$verification->set_status( $verification_details->getLastResponse()->getCurrentStep() );
								$verification->save();

								if ( ! $verification->is_success() ) {
									throw new \Exception( __( 'Invalid verification status.', 'sheerid-for-woocommerce' ) );
								}
							}
						} else {
							throw new \Exception( __( 'Invalid verification status.', 'sheerid-for-woocommerce' ) );
						}
					} catch ( \Exception $e ) {
						$valid = false;
						if ( function_exists( 'wc_add_notice' ) ) {
							$notice = sprintf( __( 'Please verify your identity before adding %s to your cart.', 'sheerid-for-woocommerce' ), $product->get_name() );
							if ( $settings->is_verification_link_enabled() ) {
								$notice .= '&nbsp;' . sprintf( __( 'Click %1$shere%2$s to verify your identity.', 'sheerid-for-woocommerce' ),
										'<a class="wcSheerIDButton"' . ' ' . $settings->get_element_attributes() . '>',
										'</a>'
									);
							}
							wc_add_notice( $notice, 'error' );
						}
					}
				}
			}
		}

		return $valid;
	}

	public function validate_checkout( $data, $errors ) {
		$cart = WC()->cart;
		if ( $cart ) {
			// check if there are any products which have a program assigned to them.
			foreach ( $cart->get_cart() as $cart_item_key => $values ) {
				/**
				 * @var \WC_Product $product
				 */
				$product = $values['data'];
				if ( $product ) {
					$options = new ProductOptions( $product );
					if ( ! $options->has_program() && $options->has_category_settings() ) {
						$options = $options->get_category_settings();
					}
					if ( $options->has_program() ) {
						try {
							$program = sheerid_wc_get_program( [ 'program_id' => $options->get_option( 'program' ) ] );
							if ( $program ) {
								$verification = sheerid_wc_get_verification( [
									'program_id' => $options->get_option( 'program_id' ),
									'user_id'    => WC()->session->get_customer_id(),
									'mode'       => $program->get_mode()
								] );
								if ( $verification && $verification->is_valid() ) {
									if ( ! \in_array( $verification->get_status(), [ 'success', 'error' ] ) ) {
										// fetch the verification from SheerID and check its status.
										$verification_details = $this->client->verificationDetails->retrieve( $verification->get_verification_id() );
										if ( is_wp_error( $verification_details ) ) {
											throw new \Exception( $verification_details->get_error_message() );
										}
										if ( $verification_details->getLastResponse()->getCurrentStep() !== 'success' ) {
											throw new \Exception( __( 'Invalid verification status.', 'sheerid-for-woocommerce' ) );
										}
									} else {
										if ( $verification->get_status() === 'error' ) {
											throw new \Exception( 'invalid' );
										}
									}
								} else {
									throw new \Exception( 'invalid' );
								}
							}
						} catch ( \Exception $e ) {
							$notice = '&nbsp;' . sprintf(
									__( 'Click %1$shere%2$s to verify your identity before purchasing %3$s.', 'sheerid-for-woocommerce' ),
									'<a class="wcSheerIDButton"' . ' ' . $options->get_element_attributes( $settings ) . '>',
									'</a>',
									$product->get_name()
								);
							$errors->add( 'program_validation', $notice );
						}
					}
				}
			}
		}
	}

}