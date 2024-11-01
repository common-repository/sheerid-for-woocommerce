<?php

namespace WooCommerce\SheerID\ServiceProviders;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\AdminAjaxController;
use WooCommerce\SheerID\Admin\AdminMenu;
use WooCommerce\SheerID\Admin\AdminSettingsController;
use WooCommerce\SheerID\Admin\AdminSettingsRegistry;
use WooCommerce\SheerID\Admin\AdminTaxonomies;
use WooCommerce\SheerID\Admin\MetaBox\MetaBoxCouponData;
use WooCommerce\SheerID\Admin\MetaBox\MetaBoxProductData;
use WooCommerce\SheerID\Admin\ProgramsTable;
use WooCommerce\SheerID\Admin\Settings\APISettings;
use WooCommerce\SheerID\Admin\Settings\CheckoutSettings;
use WooCommerce\SheerID\Admin\Settings\EmailSettings;
use WooCommerce\SheerID\Admin\Settings\ProgramSettings;
use WooCommerce\SheerID\Admin\Settings\Verifications;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Logger;

/**
 * Service Provider that registers and initializes classes responsible for managing admin settings
 */
class AdminSettingsServiceProvider extends AbstractServiceProvider {

	public function register() {
		/**
		 * Settings
		 */
		$this->container->register( APISettings::class, function ( $container ) {
			return new APISettings();
		} );

		$this->container->register( ProgramSettings::class, function ( $container ) {
			return new ProgramSettings( $container->get( BaseClient::class ) );
		} );

		$this->container->register( EmailSettings::class, function ( $container ) {
			return new EmailSettings();
		} );

		$this->container->register( Verifications::class, function ( $container ) {
			return new Verifications( $container->get( BaseClient::class ) );
		} );

		$this->container->register( CheckoutSettings::class, function ( $container ) {
			return new CheckoutSettings( $container->get( BaseClient::class ) );
		} );

		$this->container->register( AdminMenu::class, function () {
			return new AdminMenu();
		} );

		$this->container->register( AdminSettingsRegistry::class, function () {
			return new AdminSettingsRegistry();
		} );

		$this->container->register( AdminSettingsController::class, function ( $container ) {
			$ctrl = new AdminSettingsController(
				$container->get( AdminSettingsRegistry::class ),
				$container->get( AssetsApi::class )
			);
			$ctrl->set_settings( [
				$container->get( APISettings::class ),
				$container->get( ProgramSettings::class ),
				$container->get( Verifications::class ),
				$container->get( CheckoutSettings::class )
			] );

			return $ctrl;
		} );

		$this->container->register( MetaBoxCouponData::class, function ( $container ) {
			return new MetaBoxCouponData(
				$container->get( BaseClient::class ),
				$container->get( AssetsApi::class )
			);
		} );

		$this->container->register( MetaBoxProductData::class, function ( $container ) {
			return new MetaBoxProductData(
				$container->get( BaseClient::class ),
				$container->get( AssetsApi::class )
			);
		} );

		$this->container->register( AdminAjaxController::class, function ( $container ) {
			return new AdminAjaxController(
				$container->get( BaseClient::class ),
				$container->get( Logger::class )
			);
		} );

		$this->container->register( AdminTaxonomies::class, function ( $container ) {
			return new AdminTaxonomies(
				$container->get( BaseClient::class ),
				$container->get( AssetsApi::class )
			);
		} );
	}

	public function initialize() {
		$this->container->get( APISettings::class );
		if ( is_admin() ) {
			$this->container->get( AdminSettingsController::class )->initialize();
			$this->container->get( AdminMenu::class );
			$this->container->get( MetaBoxCouponData::class )->initialize();
			$this->container->get( MetaBoxProductData::class )->initialize();
			$this->container->get( AdminAjaxController::class )->initialize();
			$this->container->get( AdminTaxonomies::class )->initialize();
		}
	}

}