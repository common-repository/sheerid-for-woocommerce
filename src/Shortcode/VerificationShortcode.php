<?php

namespace WooCommerce\SheerID\Shortcode;

use WooCommerce\SheerID\Constants;
use WooCommerce\SheerID\Utils\JavascriptData;

class VerificationShortcode extends AbstractShortcode {

	protected $id = 'sheerid_verification';

	public function enqueue_scripts() {
		wp_enqueue_script( 'wc-sheerid-shortcode' );
		wp_enqueue_style( Constants::SHEER_ID_STYLES );
	}

	public function render() {
		$data = [
			'program' => $this->attributes->get( 'program' ),
			'text'    => [
				'loading' => __( 'Processing...', 'sheerid-for-woocommerce' ),
				'label'   => $this->attributes->get( 'label' )
			]
		];
		$html = '<div class="wc-sheerid-shortcode__container" data-sheerid="' . \wc_esc_json( wp_json_encode( JavascriptData::get_verification_data( $data ) ) ) . '">';
		$html .= $this->templates->load_template_html( 'verification-button.php', [
			'attributes' => $this->attributes
		] );
		$html .= '</div>';

		return $html;
	}

	public function get_shortcode_attributes() {
		return [
			'label'            => [
				'default' => esc_html__( 'Verify Status', 'sheerid-for-woocommerce' )
			],
			'program'          => [
				'default' => __( 'Verify Status', 'sheerid-for-woocommerce' )
			],
			'class'            => [
				'default' => ''
			],
			'color'            => [
				'default' => '#fff'
			],
			'background-color' => [
				'default' => '#5a5aff'
			],
			'padding'          => [
				'default' => '10px 20px'
			],
			'border-radius'    => [
				'default' => '4px'
			],
			'cursor'           => [
				'default' => 'pointer'
			]
		];
	}

}