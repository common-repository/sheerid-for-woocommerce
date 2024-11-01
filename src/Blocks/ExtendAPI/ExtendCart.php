<?php

namespace WooCommerce\SheerID\Blocks\ExtendAPI;

class ExtendCart {

	private $controller;

	private $needs_data = false;

	private $data;

	public function __construct( \WooCommerce\SheerID\VerificationController $controller ) {
		$this->controller = $controller;
	}

	public function initialize() {
		$this->add_endpoint_data();
	}

	public function set_needs_data( $bool ) {
		$this->needs_data = $bool;
	}

	public function set_data( $data ) {
		$this->data = $data;
	}

	private function add_endpoint_data() {
		woocommerce_store_api_register_endpoint_data( [
			'endpoint'      => \Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema::IDENTIFIER,
			'namespace'     => 'sheerId',
			'data_callback' => function () {
				if ( $this->data ) {
					return [
						'data' => $this->data
					];
				}

				return [];
			},
			'schema_type'   => ARRAY_A
		] );
	}

}