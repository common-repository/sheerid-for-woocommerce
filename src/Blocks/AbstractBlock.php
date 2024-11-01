<?php

namespace WooCommerce\SheerID\Blocks;

use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Assets\Config;

class AbstractBlock {

	protected $assets;

	protected $block_name = '';

	public function __construct( AssetsApi $assets ) {
		$this->assets = $assets;
	}

	public function initialize() {
		$this->register_block_type();
		$this->register_editor_scripts();
		$this->register_frontend_scripts();
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_scripts' ] );
	}


	public function get_name() {
		return $this->block_name;
	}

	protected function get_editor_script_handle() {
		return '';
	}

	protected function get_frontend_script_handle() {
	}

	protected function register_editor_scripts() {
	}

	protected function register_frontend_scripts() {
	}

	public function enqueue_editor_scripts() {
		wp_enqueue_script( $this->get_editor_script_handle() );
	}

	public function enqueue_frontend_scripts() {
		wp_enqueue_script( $this->get_frontend_script_handle() );
	}

	protected function register_block_type() {
		register_block_type_from_metadata( $this->assets->get_base_path() . 'assets/build/' . $this->get_name() . '-block.json', [
			'render_callback' => [ $this, 'render_block' ],
			'editor_script'   => $this->get_editor_script_handle(),
		] );
	}

	public function render_block( $attributes = [], $content = '' ) {
		if ( ! is_admin() ) {
			$this->enqueue_frontend_scripts();
		}

		return $this->render( $attributes, $content );
	}

	public function render( $attributes, $content ) {
		return $content;
	}

}