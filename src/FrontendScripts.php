<?php

namespace WooCommerce\SheerID;

use WooCommerce\SheerID\Assets\AssetsApi;

/**
 *
 */
class FrontendScripts {

	private $assets;

	public function __construct( AssetsApi $assets ) {
		$this->assets = $assets;
	}

	public function initialize() {
		$this->register_scripts();
	}

	public function register_scripts() {
		$this->assets->register_script( 'wc-sheerid-helpers', 'assets/build/helpers.js', [ 'wc-sheerid-frontend-commons' ] );

		$this->assets->register_script( 'wc-sheerid-shortcode', 'assets/build/shortcode.js' );

		$this->assets->register_script( 'wc-sheerid-frontend-commons', 'assets/build/frontend-commons.js' );

		$this->assets->register_script( 'wc-sheerid-product-verification', 'assets/build/product-verification.js' );

		$this->assets->register_script( 'wc-sheerid-checkout-verification', 'assets/build/checkout-verification.js' );

		$this->assets->register_style( 'wc-sheerid-frontend-styles', 'assets/build/styles.css' );

		//phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		\wp_register_script( Constants::SHEER_ID_EXTERNAL, 'https://cdn.jsdelivr.net/npm/@sheerid/jslib@1/sheerid.js', [], false, true );

		//phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		\wp_register_style( Constants::SHEER_ID_STYLES, 'https://cdn.jsdelivr.net/npm/@sheerid/jslib@1/sheerid.css' );
	}

}