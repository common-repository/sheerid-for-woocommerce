<?php

namespace WooCommerce\SheerID\Blocks;

use WooCommerce\SheerID\Assets\AssetsApi;

class BlocksController {

	public function initialize() {
		$this->register_blocks();;
		$this->register_checkout_blocks();
	}

	private function register_blocks() {
		foreach ( $this->get_blocks() as $clazz ) {
			if ( class_exists( $clazz ) ) {
				$block = new $clazz( sheerid_wc_container()->get( AssetsApi::class ) );
				$block->initialize();
			}
		}
	}

	private function register_checkout_blocks() {
		add_action( 'woocommerce_blocks_checkout_block_registration', function ( $registry ) {
			$registry->register( new CheckoutBlock( sheerid_wc_container()->get( AssetsApi::class ) ) );
		} );
	}

	private function get_blocks() {
		return [
			'WooCommerce\SheerID\Blocks\VerificationBlock'
		];
	}

}