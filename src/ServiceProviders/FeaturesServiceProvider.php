<?php

namespace WooCommerce\SheerID\ServiceProviders;

use WooCommerce\SheerID\Plugin;

class FeaturesServiceProvider extends AbstractServiceProvider {

	public function register() {
		$this->custom_order_tables_support();
	}

	public function initialize() {
		// TODO: Implement initialize() method.
	}

	private function custom_order_tables_support() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			add_action( 'before_woocommerce_init', function () {
				try {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->container->get( Plugin::class )->get_plugin_file() );
				} catch ( \Exception $e ) {
				}
			} );
		}
	}

}