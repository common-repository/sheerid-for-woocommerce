<?php

namespace WooCommerce\SheerID\Shortcode;

class ShortcodeController {

	private $registry;

	private $container;

	public function __construct( ShortcodeRegistry $registry, $container ) {
		$this->registry  = $registry;
		$this->container = $container;
	}

	public function initialize() {
		add_action( 'woocommerce_sheerid_shortcode_registration', [ $this, 'register_shortcodes' ] );
		add_action( 'wp_print_scripts', [ $this, 'add_script_data' ], 5 );
		add_action( 'wp_print_footer_scripts', [ $this, 'add_script_data' ], 5 );
		$this->registry->initialize();
		$this->register_scripts();
	}

	public function register_shortcodes() {
		foreach ( $this->get_shortcodes() as $clazz ) {
			$shortcode = $this->container->get( $clazz );
			$this->registry->register( $shortcode );
			add_shortcode( $shortcode->get_id(), [ $shortcode, 'render_shortcode' ] );
		}
	}

	public function register_scripts() {
		foreach ( $this->registry->get_registered_integrations() as $shortcode ) {
			$shortcode->register_scripts();
		}
	}

	public function add_script_data() {
		foreach ( $this->registry->get_registered_integrations() as $shortcode ) {
			if ( $shortcode->is_active() && ! $shortcode->get_script_data_added() ) {
				if ( $shortcode->get_attributes() ) {
					$shortcode->add_script_data();
					$shortcode->set_script_data_added( true );
				}
			}
		}
	}

	private function get_shortcodes() {
		return [
			VerificationShortcode::class
		];
	}

}