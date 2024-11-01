<?php

namespace WooCommerce\SheerID;

class ContextDataController {

	const CHECKOUT = 'checkout';

	const CART = 'cart';

	const PRODUCT = 'product';

	const SHOP = 'shop';

	private $context;

	/**
	 * @var \WC_Product
	 */
	private $product;

	/**
	 * @var \WC_Coupon
	 */
	private $coupon;

	public function initialize() {
		add_action( 'wp_loaded', [ $this, 'init_context' ] );
		add_filter( 'woocommerce_sheerid_get_verification_data', [ $this, 'add_context_data' ] );
		add_filter( 'woocommerce_sheerid_get_messages', [ $this, 'update_contextual_messages' ], 10, 3 );
	}

	public function init_context() {
		if ( ! $this->context ) {
			if ( \is_checkout() ) {
				$this->context = self::CHECKOUT;
			} elseif ( \is_cart() || doing_action( 'wc_ajax_apply_coupon' ) ) {
				$this->context = self::CART;
			} elseif ( $this->is_product() ) {
				$this->context = self::PRODUCT;
			} elseif ( \is_shop() ) {
				$this->context = self::SHOP;
			}
		}
	}

	public function add_context_data( $data ) {
		$this->init_context();
		if ( empty( $data['context_args']['context'] ) ) {
			$data['context_args']['context'] = $this->context;
		}

		if ( $this->is_product() || $this->product ) {
			if ( $this->product ) {
				$data['context_args']['product_id'] = $this->product->get_id();
			} else {
				$data['context_args']['product_id'] = get_queried_object_id();
			}
		} elseif ( $this->is_shop() ) {
			$data['context_args']['context'] = ' shop';
		} elseif ( $this->is_cart() || $this->coupon ) {
			if ( $this->coupon ) {
				$data['context_args']['coupon_id'] = $this->coupon->get_id();
			}
		}

		return $data;
	}

	/**
	 * @param array                             $messages
	 * @param array                             $context_args
	 * @param \WooCommerce\SheerID\Verification $verification
	 *
	 * @return mixed
	 */
	public function update_contextual_messages( $messages, $context_args, $verification ) {
		if ( isset( $context_args['context'] ) ) {
			$this->context = $context_args['context'];

			$segment = $verification->get_segment();

			if ( $this->is_product() ) {
				$product                                       = wc_get_product( $context_args['product_id'] );
				$messages[ $segment ]['step.success.subtitle'] = sprintf( __( 'You can now add %1$s to your shopping cart.', 'sheerid-for-woocommerce' ), $product->get_name() );
			} elseif ( $this->is_cart() ) {
				$coupon                                        = new \WC_Coupon( $context_args['coupon_id'] );
				$messages[ $segment ]['step.success.subtitle'] = sprintf( __( 'You can now add coupon %1$s to your shopping cart.', 'sheerid-for-woocommerce' ), $coupon->get_code() );
			} elseif ( $this->is_checkout() ) {
				$messages[ $segment ]['step.success.subtitle']      = __( 'You may now proceed with your order.', 'sheerid-for-woocommerce' );
				$messages[ $segment ]['step.personalInfo.subtitle'] = __( 'Please verify your identity before proceeding with your order.', 'sheerid-for-woocommerce' );
			} elseif ( $this->is_coupon() ) {
				//$coupon                            = new \WC_Coupon( $context_args['coupon'] );
				$messages[ $segment ]['step.success.subtitle'] = sprintf( __( 'Coupon %1$s can now be used.', 'sheerid-for-woocommerce' ), $context_args['coupon'] );
			}
		}

		return $messages;
	}

	private function is_checkout() {
		return $this->context === 'checkout' || \is_checkout();
	}

	private function is_cart() {
		return $this->context === 'cart' || \is_cart();
	}

	private function is_shop() {
		return $this->context === 'shop' || \is_shop();
	}

	private function is_product() {
		return $this->context === 'product' || \is_product();
	}

	private function is_coupon() {
		return $this->context === 'coupon';
	}

	public function set_context( $context ) {
		$this->context = $context;
	}

	public function set_product( $product ) {
		$this->product = $product;
	}

	public function set_coupon( $coupon ) {
		$this->coupon = $coupon;
	}

}