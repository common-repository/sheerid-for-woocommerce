<?php


namespace WooCommerce\SheerID\Assets;


class Config {

	private $version;

	private $base_path;

	private $url;

	/**
	 * Config constructor.
	 *
	 * @param string $version
	 * @param string $plugin_path
	 */
	public function __construct( string $version, string $base_path ) {
		$this->version   = $version;
		$this->base_path = $base_path;
		$this->url       = plugin_dir_url( trailingslashit( $base_path . '/src' ) );
	}

	public function get_path( $relative_path = '' ) {
		return trailingslashit( $this->base_path ) . $relative_path;
	}

	public function get_url() {
		return $this->url;
	}

	public function version() {
		return $this->version;
	}

}