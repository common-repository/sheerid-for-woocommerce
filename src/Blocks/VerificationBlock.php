<?php

namespace WooCommerce\SheerID\Blocks;

use WooCommerce\SheerID\Constants;
use WooCommerce\SheerID\Utils\JavascriptData;

class VerificationBlock extends AbstractBlock {

	protected $block_name = 'verification';

	protected function get_editor_script_handle() {
		return 'wc-sheerid-verification-block-editor';
	}

	protected function get_frontend_script_handle() {
		return 'wc-sheerid-verification-block';
	}

	protected function register_editor_scripts() {
		$this->assets->register_script( $this->get_editor_script_handle(), 'assets/build/verification-block-editor.js' );
	}

	protected function register_frontend_scripts() {
		$this->assets->register_script( $this->get_frontend_script_handle(), 'assets/build/verification-block.js' );
	}

	public function enqueue_frontend_scripts() {
		parent::enqueue_frontend_scripts();
		wp_enqueue_style( Constants::SHEER_ID_STYLES );
	}

	public function render( $attributes, $content ) {
		preg_match( '/<a[^>]+>(.+)+<\/a>/', $content, $matches );
		$context = ! empty( $attributes['coupon'] ) ? 'coupon' : '';
		$data    = wc_esc_json( wp_json_encode( JavascriptData::get_verification_data( [
			'program'      => $attributes['program'],
			'context_args' => [
				'context' => $context,
				'coupon'  => $attributes['coupon']
			],
			'text'         => [
				'label'   => ! empty( $matches[1] ) ? $matches[1] : __( 'Verify', 'sheerid-for-woocommerce' ),
				'loading' => ! empty( $attributes['loading_text'] ) ? $attributes['loading_text'] : __( 'Processing...', 'sheerid-for-woocommerce' )
			]
		] ) ) );
		$content = '<div class="wc-sheerid-button-block__container" data-sheerid="' . $data . '">' . $content . '</div>';

		return $content;
	}


}