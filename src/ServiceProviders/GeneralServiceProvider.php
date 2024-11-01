<?php

namespace WooCommerce\SheerID\ServiceProviders;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\Settings\APISettings;
use WooCommerce\SheerID\Admin\Settings\CheckoutSettings;
use WooCommerce\SheerID\Assets\AssetData;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Assets\Config;
use WooCommerce\SheerID\Blocks\BlocksController;
use WooCommerce\SheerID\Blocks\ExtendAPI\ExtendCart;
use WooCommerce\SheerID\CartController;
use WooCommerce\SheerID\CheckoutController;
use WooCommerce\SheerID\ContextDataController;
use WooCommerce\SheerID\CouponController;
use WooCommerce\SheerID\CustomerController;
use WooCommerce\SheerID\Database\DataStoreController;
use WooCommerce\SheerID\FrontendScripts;
use WooCommerce\SheerID\Install;
use WooCommerce\SheerID\Logger;
use WooCommerce\SheerID\MessageMap;
use WooCommerce\SheerID\Package\PackageController;
use WooCommerce\SheerID\Package\PackageRegistry;
use WooCommerce\SheerID\Plugin;
use WooCommerce\SheerID\SheerIDClient;
use WooCommerce\SheerID\TemplateLoader;
use WooCommerce\SheerID\VerificationController;

class GeneralServiceProvider extends AbstractServiceProvider {

	public function register() {
		$this->load_text_domain();

		require_once $this->container->get( Plugin::class )->get_base_path() . 'src/wc-sheerid-functions.php';

		$this->container->register( BaseClient::class, function ( $container ) {
			$client = new SheerIDClient( [
				'token' => $container->get( APISettings::class )->get_access_token(),
			] );
			$client->set_log( $container->get( Logger::class ) );

			return $client;
		} );

		$this->container->register( Logger::class, function () {
			return new Logger( 'wc-sheerid' );
		} );

		$this->container->register( AssetsApi::class, function ( $container ) {
			return new AssetsApi( new Config(
				$container->get( Plugin::class )->version(),
				dirname( __DIR__, 2 )
			) );
		} );

		$this->container->register( FrontendScripts::class, function ( $container ) {
			return new FrontendScripts( $container->get( AssetsApi::class ) );
		} );

		$this->container->register( TemplateLoader::class, function ( $container ) {
			return new TemplateLoader(
				$container->get( Plugin::class )->get_base_path(),
				'sheerid-for-woocommerce'
			);
		} );

		$this->container->register( CouponController::class, function ( $container ) {
			return new CouponController(
				$container->get( BaseClient::class ),
				$container->get( ContextDataController::class )
			);
		} );

		$this->container->register( Install::class, function ( $container ) {
			return new Install( $container->get( Plugin::class )->version() );
		} );

		$this->container->register( DataStoreController::class, function ( $container ) {
			return new DataStoreController();
		} );

		$this->container->register( CustomerController::class, function ( $container ) {
			return new CustomerController( $container->get( BaseClient::class ) );
		} );

		$this->container->register( PackageController::class, function ( $container ) {
			return new PackageController( $container->get( PackageRegistry::class ) );
		} );

		$this->container->register( PackageRegistry::class, function ( $container ) {
			return new PackageRegistry();
		} );

		$this->container->register( BlocksController::class, function ( $container ) {
			return new BlocksController();
		} );

		$this->container->register( MessageMap::class, function () {
			return new MessageMap();
		} );

		$this->container->register( CartController::class, function ( $container ) {
			return new CartController(
				$container->get( BaseClient::class ),
				$container->get( AssetsApi::class )
			);
		} );

		$this->container->register( ContextDataController::class, function () {
			return new ContextDataController();
		} );

		$this->container->register( CheckoutController::class, function ( $container ) {
			return new CheckoutController(
				$container->get( BaseClient::class ),
				$container->get( CheckoutSettings::class ),
				new AssetData()
			);
		} );

		$this->container->register( VerificationController::class, function ( $container ) {
			return new VerificationController(
				$container->get( BaseClient::class ),
				$container->get( MessageMap::class )
			);
		} );

		$this->container->register( ExtendCart::class, function ( $container ) {
			return new ExtendCart( $container->get( VerificationController::class ) );
		} );
	}

	public function initialize() {
		$this->container->get( FrontendScripts::class )->initialize();
		$this->container->get( CouponController::class )->initialize();
		$this->container->get( Install::class )->initialize();
		$this->container->get( DataStoreController::class )->initialize();
		$this->container->get( CustomerController::class )->initialize();
		$this->container->get( PackageController::class )->initialize();
		$this->container->get( BlocksController::class )->initialize();
		$this->container->get( MessageMap::class )->initialize();
		$this->container->get( CartController::class )->initialize();
		$this->container->get( ContextDataController::class )->initialize();
		$this->container->get( CheckoutController::class )->initialize();
		$this->container->get( ExtendCart::class )->initialize();
	}

	private function load_text_domain() {
		\load_plugin_textdomain( 'sheerid-for-woocommerce', false, dirname( plugin_basename( $this->container->get( Plugin::class )->get_plugin_file() ) ) . '/i18n/languages' );
	}

}