<?php

namespace WooCommerce\SheerID\Registry;

class BaseRegistry implements RegistryInterface {

	protected $id;

	protected $registry = [];

	public function initialize() {
		if ( ! $this->id ) {
			throw new \Exception( 'Registries must have a valid ID assigned.' );
		}
		do_action( 'woocommerce_sheerid_' . $this->id . '_registration', $this );
	}

	public function register( $integration ) {
		$this->registry[ $integration->get_id() ] = $integration;
	}

	public function get( $id ) {
		return $this->registry[ $id ] ?? null;
	}

	public function get_registered_integrations() {
		return $this->registry;
	}

}