<?php

namespace WooCommerce\SheerID\ServiceProviders;

use WooCommerce\SheerID\Assets\AssetData;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Shortcode\ShortcodeController;
use WooCommerce\SheerID\Shortcode\ShortcodeRegistry;
use WooCommerce\SheerID\Shortcode\VerificationShortcode;
use WooCommerce\SheerID\TemplateLoader;

class ShortcodeServiceProvider extends AbstractServiceProvider {

	public function register() {
		$this->container->register( ShortcodeController::class, function ( $container ) {
			return new ShortcodeController(
				$container->get( ShortcodeRegistry::class ),
				$container
			);
		} );
		$this->container->register( ShortcodeRegistry::class, function ( $container ) {
			return new ShortcodeRegistry();
		} );

		$this->container->register( VerificationShortcode::class, function ( $container ) {
			return new VerificationShortcode(
				$container->get( AssetsApi::class ),
				$container->get( TemplateLoader::class ),
				new AssetData()
			);
		} );
	}

	public function initialize() {
		$this->container->get( ShortcodeController::class )->initialize();
	}

}