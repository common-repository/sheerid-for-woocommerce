<?php

namespace WooCommerce\SheerID\Assets;

class AssetData {

	private $data = [];

	public function add( $key, $value ) {
		$this->data[ $key ] = $value;
	}

	public function get( $key, $default = null ) {
		return $this->data[ $key ] ?? $default;
	}

	public function has( $key ) {
		return \array_key_exists( $key, $this->data );
	}

	public function prepare_script_data( $handle, $data = null ) {
		do_action( 'wc_sheerid_prepare_script_data', $handle, $this );
		$data = \rawurlencode( \wp_json_encode( $this->data ) );
		$name = str_replace( '-', '_', $handle );
		\wp_add_inline_script(
			$handle,
			'var ' . $name . ' = JSON.parse(decodeURIComponent("' . esc_js( $data ) . '"))',
			'before'
		);
	}

	public function get_data() {
		return $this->data;
	}

}