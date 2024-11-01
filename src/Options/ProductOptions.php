<?php

namespace WooCommerce\SheerID\Options;

class ProductOptions extends AbstractOptions {

	private $category_settings;

	protected $object_type = 'WC_Product';

	public function __construct( $object ) {
		parent::__construct( $object );
		$this->category_settings = array_reduce( $this->object->get_category_ids(), function ( $carry, $term_id ) {
			$option = new CategoryOptions( $term_id );
			if ( $option->has_program() ) {
				$carry[] = $option;
			}

			return $carry;
		}, [] );
	}

	protected function get_object( $id ) {
		return wc_get_product( $id );
	}

	public function get_product() {
		return $this->object;
	}

	public function get_default_settings() {
		return [
			'program'             => 'none',
			'require_before_cart' => 'no',
			'click_here'          => 'no',
			'click_here_behavior' => 'stay',
			'redirect_page'       => ''
		];
	}

	public function has_program() {
		return $this->get_option( 'program' ) !== 'none';
	}

	public function get_program_id() {
		return $this->get_option( 'program' );
	}

	public function get_redirect_page_id() {
		return $this->get_option( 'redirect_page' );
	}

	public function is_require_before_cart() {
		return \wc_string_to_bool( $this->get_option( 'require_before_cart' ) );
	}

	public function is_verification_link_enabled() {
		return \wc_string_to_bool( $this->get_option( 'click_here' ) );
	}

	public function is_redirect_enabled() {
		return $this->get_option( 'click_here_behavior' ) === 'redirect';
	}

	public function has_category_settings() {
		return ! empty( $this->category_settings );
	}

	/**
	 * @return false|\WooCommerce\SheerID\Options\CategoryOptions
	 */
	public function get_category_settings() {
		return ! empty( $this->category_settings ) ? $this->category_settings[0] : null;
	}

}