<?php

namespace WooCommerce\SheerID\Container;

use WooCommerce\SheerID\ServiceProviders\AdminSettingsServiceProvider;
use WooCommerce\SheerID\ServiceProviders\EmailServiceProvider;
use WooCommerce\SheerID\ServiceProviders\FeaturesServiceProvider;
use WooCommerce\SheerID\ServiceProviders\GeneralServiceProvider;
use WooCommerce\SheerID\ServiceProviders\RestServiceProvider;
use WooCommerce\SheerID\ServiceProviders\ShortcodeServiceProvider;

/**
 * Container class which loads all registered dependencies
 */
class BaseContainer {

	private $resolvers = [];

	private $service_providers = [
		AdminSettingsServiceProvider::class,
		RestServiceProvider::class,
		GeneralServiceProvider::class,
		ShortcodeServiceProvider::class,
		EmailServiceProvider::class,
		FeaturesServiceProvider::class
	];

	private $provider_instances = [];

	public function get( $key ) {
		if ( ! array_key_exists( $key, $this->resolvers ) ) {
			throw new \Exception( esc_html( sprintf( 'Invalid key %s. It has not been registered.', $key ) ) );
		}

		return $this->resolvers[ $key ]->get( $this );
	}

	public function register( $key, $callback, $singleton = true ) {
		$this->resolvers[ $key ] = new BaseResolver( $callback, $singleton );
	}

	public function register_services() {
		foreach ( $this->service_providers as $clazz ) {
			$service_provider = new $clazz( $this );
			$service_provider->register();
			$this->provider_instances[] = $service_provider;
		}
	}

	public function initialize_services() {
		foreach ( $this->provider_instances as $instance ) {
			$instance->initialize();
		}
	}

}