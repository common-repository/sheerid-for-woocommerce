<?php

namespace WooCommerce\SheerID\Options;

use WooCommerce\SheerID\Constants;
use WooCommerce\SheerID\Utils\JavascriptData;

abstract class AbstractOptions {

	/**
	 * @var \WC_Data
	 */
	protected $object;

	protected $settings;

	protected $object_type;

	public function __construct( $object ) {
		if ( is_a( $object, $this->object_type ) ) {
			$this->object = $object;
		} elseif ( is_int( $object ) ) {
			$this->object = $this->get_object( $object );
		}
	}

	abstract protected function get_object( $id );

	protected function initialize_settings() {
		$this->settings = $this->object->get_meta( '_sheerid_options' );
		if ( ! is_array( $this->settings ) ) {
			$this->settings = $this->get_default_settings();
		}
	}

	public function get_option( $key, $default = null ) {
		if ( $this->settings === null ) {
			$this->initialize_settings();
		}
		if ( ! array_key_exists( $key, $this->settings ) ) {
			$this->settings[ $key ] = $default;
		}

		return $this->settings[ $key ];
	}

	public function update_option( $key, $value ) {
		$this->settings[ $key ] = $value;
	}

	public function save() {
	}

	public function get_default_settings() {
		return [];
	}

	public function has_program() {
		return $this->get_option( Constants::SHEERID_PROGRAM ) !== 'none';
	}

	public function get_redirect_page_id() {
		return $this->get_option( 'redirect_page' );
	}

	public function is_redirect_enabled() {
		return false;
	}

	public function get_program_id() {
		return '';
	}

	public function is_verification_link_enabled() {
		return false;
	}

	public function get_element_attributes( $attributes = [] ) {
		$converted_attributes = [];
		$attributes           = \wp_parse_args( [ 'class' => 'wcSheerIDButton' ], $attributes );
		if ( $this->is_redirect_enabled() ) {
			$attributes['href']          = esc_url( get_page_link( $this->get_redirect_page_id() ) );
			$attributes['data-redirect'] = 'true';
		} else {
			$attributes['href']         = '';
			$attributes['data-sheerid'] = JavascriptData::get_html_data(
				JavascriptData::get_verification_data(
					[ 'program' => $this->get_program_id() ]
				) );
		}

		foreach ( $attributes as $key => $attribute ) {
			$converted_attributes[] = sprintf( '%1$s="%2$s"', esc_attr( $key ), $attribute );
		}

		return implode( ' ', $converted_attributes );
	}

}