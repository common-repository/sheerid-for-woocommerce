<?php

namespace WooCommerce\SheerID\Rest;

use SheerID\Client\BaseClient;
use SheerID\Utils\Util;
use WooCommerce\SheerID\Assets\AssetData;
use WooCommerce\SheerID\Container\BaseContainer;
use WooCommerce\SheerID\Logger;
use WooCommerce\SheerID\MessageMap;
use WooCommerce\SheerID\Rest\Route\Admin\SegmentsRoute;
use WooCommerce\SheerID\Rest\Route\VerificationRoute;
use WooCommerce\SheerID\Rest\Route\WebhookRoute;
use WooCommerce\SheerID\VerificationController;

class RouteController {

	private $container;

	public function __construct( BaseContainer $container ) {
		$this->container = $container;
	}

	public function initialize() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_action( 'wc_ajax_wc_sheerid_frontend_request', [ $this, 'handle_ajax_request' ] );
		add_action( 'wc_sheerid_prepare_script_data', [ $this, 'add_script_data' ], 10, 2 );
		add_filter( 'woocommerce_sheerid_get_verification_data', [ $this, 'add_verification_data' ] );
	}

	public function register_routes() {
		foreach ( $this->get_routes() as $route ) {
			register_rest_route(
				trim( $route->get_namespace(), '/' ),
				trim( $route->get_path(), '/' ),
				$this->get_rest_routes( $route )
			);
		}
	}

	/**
	 * @param \WooCommerce\SheerID\Rest\Route\AbstractRoute $route
	 *
	 * @return array|array[]|\string[][]
	 */
	private function get_rest_routes( $route ) {
		$routes = $route->get_routes();
		if ( ! Util::isList( $routes ) ) {
			$routes = [ $routes ];
		}

		return array_map( function ( $args ) use ( $route ) {
			return array_merge(
				[ 'permission_callback' => $route->get_permission_callback() ],
				$args
			);
		}, $routes );
	}

	/**
	 * @return null[]
	 */
	public function get_routes() {
		return [
			'verification'   => new VerificationRoute(
				$this->container->get( VerificationController::class )
			),
			'webhook'        => new WebhookRoute(
				$this->container->get( BaseClient::class ),
				$this->container->get( Logger::class )
			),
			'admin/segments' => new SegmentsRoute( $this->container->get( BaseClient::class ) )
		];
	}

	public function add_script_data( $handle, AssetData $data ) {
		$routes = [];
		foreach ( $this->get_routes() as $key => $route ) {
			$routes[ $key ] = $this->get_route_endpoint( $route->get_rest_route() );
		}
		$data->add( 'routes', $routes );
	}

	public function add_verification_data( $data ) {
		$routes = [];
		foreach ( $this->get_routes() as $key => $route ) {
			$routes[ $key ] = $this->get_route_endpoint( $route->get_rest_route() );
		}
		$data['routes'] = $routes;

		return $data;
	}

	public function handle_ajax_request() {
		if ( isset( $_GET['sheerid_rest_path'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			global $wp;
			$wp->set_query_var( 'rest_route', sanitize_text_field( wp_unslash( $_GET['sheerid_rest_path'] ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			\rest_api_loaded();
		}
	}

	private function get_route_endpoint( $path ) {
		if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
			$endpoint = esc_url_raw( apply_filters( 'woocommerce_ajax_get_endpoint', add_query_arg( 'wc-ajax', 'wc_stripe_frontend_request', remove_query_arg( [
				'remove_item',
				'add-to-cart',
				'added-to-cart',
				'order_again',
				'_wpnonce'
			], home_url( '/', 'relative' ) ) ), 'wc_sheerid_frontend_request' ) );
		} else {
			$endpoint = \WC_AJAX::get_endpoint( 'wc_sheerid_frontend_request' );
		}

		return add_query_arg( 'sheerid_rest_path', '/' . trim( $path, '/' ), $endpoint );
	}

}