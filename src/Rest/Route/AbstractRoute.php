<?php

namespace WooCommerce\SheerID\Rest\Route;

use SheerID\Utils\Util;

abstract class AbstractRoute {

	protected $namespace = 'wc-sheerid/v1';

	protected $path;

	public function get_namespace() {
		return $this->namespace;
	}

	public function get_path() {
		return $this->path;
	}

	/**
	 * Returns the full rest path
	 *
	 * @return void
	 */
	public function get_rest_route() {
		$route = '/' . Util::trimPath( $this->get_namespace() );
		$path  = Util::trimPath( $this->get_path() );
		if ( $path ) {
			$route = $route . '/' . $path;
		}

		return $route;
	}


	public function get_permission_callback() {
		return '__return_true';
	}

	abstract public function get_routes();

	public function handle_request( \WP_REST_Request $request ) {
		try {
			$http_method = strtolower( $request->get_method() );
			$method      = "handle_{$http_method}_request";
			if ( ! \method_exists( $this, $method ) ) {
				throw new \Exception( 'Method not implemented', 405 );
			}
			$response = $this->{$method}( $request );
			if ( is_wp_error( $response ) ) {
				return $response;
			}

			return rest_ensure_response( $response );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'sheerid_rest_error', $e->getMessage(), [
				'status' => $e->getCode()
			] );
		}
	}

	public function handle_get_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

	public function handle_put_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

	public function handle_delete_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

}