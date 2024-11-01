<?php

namespace WooCommerce\SheerID\ServiceProviders;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Rest\Route\WebhookRoute;
use WooCommerce\SheerID\Rest\RouteController;

class RestServiceProvider extends AbstractServiceProvider {

	public function register() {
		$this->container->register( RouteController::class, function () {
			return new RouteController( $this->container );
		} );
	}

	public function initialize() {
		$this->container->get( RouteController::class )->initialize();
	}

}