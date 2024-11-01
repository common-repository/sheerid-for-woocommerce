<?php

namespace WooCommerce\SheerID\Rest\Route\Admin;

use WooCommerce\SheerID\Rest\Route\AbstractRoute;

class AbstractAdminRoute extends AbstractRoute {

	public function get_namespace() {
		return parent::get_namespace() . '/admin';
	}

	public function get_permission_callback() {
		return function ( $request ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return new \WP_Error( 'unauthorized', __( 'Only WooCommerce managers can access this resource.', 'sheerid-for-woocommerce' ) );
			}

			return true;
		};
	}

	public function get_routes() {
		return [];
	}

}