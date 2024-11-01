<?php

namespace WooCommerce\SheerID\Package;

use WooCommerce\SheerID\Container\BaseContainer;

abstract class AbstractPackage implements PackageInterface {

	protected $id = '';

	protected $container;

	public function get_id() {
		return $this->id;
	}

	public function set_container( BaseContainer $container ) {
		$this->container = $container;
	}

}