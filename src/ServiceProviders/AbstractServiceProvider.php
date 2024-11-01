<?php

namespace WooCommerce\SheerID\ServiceProviders;

use WooCommerce\SheerID\Container\BaseContainer;

abstract class AbstractServiceProvider implements ServiceProviderInterface {

	protected $container;

	public function __construct( BaseContainer $container ) {
		$this->container = $container;
	}

}