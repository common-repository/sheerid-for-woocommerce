<?php

namespace WooCommerce\SheerID\Package;

interface PackageInterface {

	public function get_id();

	public function is_active();

	public function register_dependencies();

	public function initialize();

}