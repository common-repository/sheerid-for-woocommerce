<?php

namespace WooCommerce\SheerID\Elementor;

use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Assets\Config;
use WooCommerce\SheerID\Package\AbstractPackage;
use WooCommerce\SheerID\Plugin;

class Package extends AbstractPackage {

	protected $id = 'elementor';

	public function is_active() {
		return did_action( 'elementor/loaded' );
	}

	public function register_dependencies() {
		$this->container->register( WidgetController::class, function ( $container ) {
			return new WidgetController(
				new AssetsApi(
					new Config( $container->get( Plugin::class )->version(), dirname( __DIR__ ) )
				)
			);
		} );
	}

	public function initialize() {
		$this->container->get( WidgetController::class )->initialize();
	}

}