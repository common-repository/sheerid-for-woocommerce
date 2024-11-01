<?php

namespace WooCommerce\SheerID\Elementor;

use WooCommerce\SheerID\Assets\AssetsApi;

class WidgetController {

	private $assets;

	public function __construct( AssetsApi $assets ) {
		$this->assets = $assets;
	}

	public function initialize() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
	}

	public function register_widgets( $widgets ) {
		foreach ( $this->get_widgets() as $widget ) {
			$widgets->register( new $widget() );
		}
	}

	public function register_scripts() {
		$this->assets->register_script( 'wc-sheerid-elementor-button', 'build/button-widget.js' );
	}

	private function get_widgets() {
		return [
			'WooCommerce\SheerID\Elementor\Widget\VerificationButtonWidget'
		];
	}

}