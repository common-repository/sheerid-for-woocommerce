<?php

namespace WooCommerce\SheerID\Admin;

class AdminMenu {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_head', [ $this, 'remove_submenus' ], 900 );
	}

	public function add_menu() {
		add_submenu_page( 'woocommerce', '', __( 'SheerID', 'sheerid-for-woocommerce' ), 'manage_woocommerce', 'wc-sheerid_api', [ $this, 'output' ] );
		add_submenu_page( 'woocommerce', __( 'SheerID', 'sheerid-for-woocommerce' ), '', 'manage_woocommerce', 'wc-sheerid_program', [ $this, 'output' ] );
		add_submenu_page( 'woocommerce', __( 'SheerID', 'sheerid-for-woocommerce' ), '', 'manage_woocommerce', 'wc-sheerid_verifications', [ $this, 'output' ] );
		add_submenu_page( 'woocommerce', __( 'SheerID', 'sheerid-for-woocommerce' ), '', 'manage_woocommerce', 'wc-sheerid_checkout', [ $this, 'output' ] );
	}

	public function remove_submenus() {
		remove_submenu_page( 'woocommerce', 'wc-sheerid_program' );
		remove_submenu_page( 'woocommerce', 'wc-sheerid_verifications' );
		remove_submenu_page( 'woocommerce', 'wc-sheerid_checkout' );
	}

	public function output() {
		global $sheerid_page, $sheerid_section;
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$sheerid_page = preg_replace( '/wc-/', '', sanitize_text_field( wp_unslash( $_GET['page'] ) ) );

			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['tab'] ) ) {
				//phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$sheerid_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
			}
		}
		do_action( 'wc_sheerid_admin_section', $sheerid_page, $sheerid_section );
	}

}