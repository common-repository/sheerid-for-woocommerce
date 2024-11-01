<?php

namespace WooCommerce\SheerID\Package;

class PackageController {

	private $registry;

	public function __construct( PackageRegistry $registry ) {
		$this->registry = $registry;
	}

	public function initialize() {
		add_filter( 'woocommerce_sheerid_packages_registration', [ $this, 'register_packages' ] );
		$this->registry->initialize();
	}

	public function register_packages() {
		$container = sheerid_wc_container();
		foreach ( $this->get_packages() as $package ) {
			$instance = new $package();
			$instance->set_container( $container );
			$this->registry->register( $instance );
			if ( $instance->is_active() ) {
				$instance->register_dependencies();
				$instance->initialize();
			}
		}
	}

	private function get_packages() {
		return [
			'\WooCommerce\SheerID\Elementor\Package'
		];
	}

}