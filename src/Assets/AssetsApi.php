<?php

namespace WooCommerce\SheerID\Assets;

class AssetsApi {

	private $config;

	private $version;

	public function __construct( Config $config, $version = null ) {
		$this->config  = $config;
		$this->version = $version;
	}

	public function register_script( $handle, $relative_path, $deps = [], $footer = true ) {
		$file    = str_replace( '.js', '.asset.php', $relative_path );
		$file    = $this->config->get_path( $file );
		$version = $this->version;
		if ( file_exists( $file ) ) {
			$data    = require $file;
			$deps    = isset( $data['dependencies'] ) ? array_merge( $deps, $data['dependencies'] ) : [];
			$version = $data['version'] ?? $version;
		}
		$deps = array_unique( apply_filters( 'wc_sheerid_register_script_dependencies', $deps, $handle ) );
		wp_register_script( $handle, $this->assets_url( $relative_path ), $deps, $version, $footer );
	}

	public function register_style( $handle, $relative_path, $deps = [] ) {
		wp_register_style( $handle, $this->assets_url( $relative_path ), $deps, $this->version );
	}

	public function assets_url( $relative_path = '' ) {
		$url = $this->config->get_url();
		preg_match( '/^(\.{2}\/)+/', $relative_path, $matches );
		if ( $matches ) {
			foreach ( range( 0, substr_count( $matches[0], '../' ) - 1 ) as $idx ) {
				$url = dirname( $url );
			}
			$relative_path = '/' . substr( $relative_path, strlen( $matches[0] ) );
		}

		return $url . $relative_path;
	}

	public function get_base_path() {
		return $this->config->get_path();
	}

}