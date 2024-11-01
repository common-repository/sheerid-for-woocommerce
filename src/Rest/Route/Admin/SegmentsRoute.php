<?php

namespace WooCommerce\SheerID\Rest\Route\Admin;

use SheerID\Client\BaseClient;

class SegmentsRoute extends AbstractAdminRoute {

	protected $path = '/segments';

	private $client;

	public function __construct( BaseClient $client ) {
		$this->client = $client;
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => [ $this, 'handle_request' ]
			]
		];
	}

	public function handle_get_request( \WP_REST_Request $request ) {
		$segments = $this->client->segments->all();
		if ( ! is_wp_error( $segments ) ) {
			return [ 'segments' => $segments ];
		}
	}

}