<?php

namespace WooCommerce\SheerID\Registry;

interface RegistryInterface {

	public function initialize();

	public function register( $integration );

	public function get( $id );

	public function get_registered_integrations();

}