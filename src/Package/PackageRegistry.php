<?php

namespace WooCommerce\SheerID\Package;

class PackageRegistry extends \WooCommerce\SheerID\Registry\BaseRegistry {

	protected $id = 'packages';

	public function get_active_packages() {
		return array_filter( $this->registry, function ( $package ) {
			return $package->is_active();
		} );
	}

}