<?php

namespace WooCommerce\SheerID\Blocks;

use WooCommerce\SheerID\Admin\Settings\CheckoutSettings;
use WooCommerce\SheerID\Utils\JavascriptData;

class CheckoutBlock extends AbstractBlock implements \Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface {

	protected $block_name = 'sheerIDCheckout';

	public function initialize() {
		$this->register_editor_scripts();
		$this->register_frontend_scripts();
	}

	protected function register_frontend_scripts() {
		$this->assets->register_script( 'wc-sheerid-checkout-block', 'assets/build/checkout-block.js' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_handles() {
		return [ 'wc-sheerid-checkout-block' ];
	}

	/**
	 * @inheritDoc
	 */
	public function get_editor_script_handles() {
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_data() {
		return JavascriptData::get_verification_data( [
			'program'      => wc_sheerid_container()->get( CheckoutSettings::class )->get_option( 'program', '' ),
			'context_args' => [
				'context' => 'checkout'
			]
		] );
	}

}