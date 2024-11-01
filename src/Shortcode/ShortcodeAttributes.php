<?php

namespace WooCommerce\SheerID\Shortcode;

class ShortcodeAttributes {

	private $attributes;

	private $styles = [];

	private $css = [
		'color',
		'background-color',
		'padding',
		'margin',
		'text-transform',
		'border-radius',
		'cursor',
		'background-image',
		'background-size',
		'background-repeat',
		'background-position',
		'content'
	];

	/**
	 * @param array $attributes
	 * @param array $defaults
	 */
	public function __construct( $attributes, $defaults = [] ) {
		$this->initialize_attributes( $attributes, $defaults );
	}

	private function initialize_attributes( $attributes, $defaults ) {
		$attributes = array_merge( \wp_list_pluck( $defaults, 'default' ), $attributes );
		foreach ( $attributes as $k => $v ) {
			if ( in_array( $k, $this->css, true ) ) {
				$this->styles[] = $k . ':' . $v;
			} else {
				$this->set( $k, $v );
			}
		}
	}

	public function get( $key, $default = null ) {
		if ( ! $this->has( $key ) ) {
			$this->set( $key, $default );
		}

		return $this->attributes[ $key ];
	}

	public function set( $key, $value ) {
		$this->attributes[ $key ] = $value;
	}

	public function has( $key ) {
		return \array_key_exists( $key, $this->attributes );
	}

	public function get_styles() {
		return implode( ';', $this->styles );
	}

}