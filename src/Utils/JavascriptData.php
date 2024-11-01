<?php

namespace WooCommerce\SheerID\Utils;

class JavascriptData {

	public static $product;

	public static function get_verification_data( $data = [] ) {
		$data = wp_parse_args( $data, [
			'page_id'      => get_queried_object_id(),
			'blockUI'      => [
				'message'       => __( 'Processing...', 'sheerid-for-woocommerce' ),
				'css'           => [
					'border'          => 'none',
					'backgroundColor' => 'transparent'
				],
				'blockMsgClass' => 'wc-sheerid-processing-msg',
				'overlayCSS'    => [
					'background' => '#fff',
					'opacity'    => 0.6
				]
			],
			'customCss'    => '#sid-step-error .sid-btn__try-again { display: none; }',
			'context_args' => []
		] );

		if ( isset( $_REQUEST['verification_jwt'] ) ) { //phpcs:ignore WordPress.Security
			$data['jwt'] = \wc_clean( wp_unslash( $_REQUEST['verification_jwt'] ) ); //phpcs:ignore WordPress.Security
		}

		return apply_filters( 'woocommerce_sheerid_get_verification_data', $data );
	}

	public static function get_html_data( $data ) {
		return \wc_esc_json( wp_json_encode( $data ) );
	}

}