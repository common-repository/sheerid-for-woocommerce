<?php

namespace WooCommerce\SheerID\Rest\Route;

use WooCommerce\SheerID\Constants;
use WooCommerce\SheerID\MessageMap;
use WooCommerce\SheerID\SheerIDClient;
use WooCommerce\SheerID\Verification;
use WooCommerce\SheerID\VerificationController;

class VerificationRoute extends AbstractRoute {

	private $controller;

	protected $path = '/verification';

	public function __construct( VerificationController $controller ) {
		$this->controller = $controller;
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ],
				'args'     => [
					'program' => [
						'required'          => true,
						'validate_callback' => function ( $value ) {
							if ( empty( $value ) ) {
								return new \WP_Error( 'rest_invalid_param', esc_html__( 'Program property cannot be empty.', 'sheerid-for-woocommerce' ) );
							}
						}
					]
				]
			]
		];
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		$program_id   = $request->get_param( 'program' );
		$page_id      = $request->get_param( 'page_id' );
		$context_args = $request->get_param( 'context_args' );

		$response = $this->controller->create_verification( $program_id, $page_id, $context_args );

		// ensure the session with ID WC()->session->get_customer_id() is saved to the cookies.
		WC()->session->set_customer_session_cookie( true );

		return $response;
	}

}