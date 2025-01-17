<?php

namespace WooCommerce\SheerID\Options;

use WooCommerce\SheerID\Constants;

class CouponOptions extends AbstractOptions {

	protected $object_type = 'WC_Coupon';

	protected function get_object( $id ) {
		return new \WC_Coupon( $id );
	}

	public function get_default_settings() {
		return [
			Constants::SHEERID_ENABLED           => false,
			Constants::SHEERID_PROGRAM           => '',
			Constants::SHEERID_ENABLE_LINK       => true,
			Constants::SHEERID_LINK_TYPE         => 'stay',
			Constants::SHEERID_VERIFICATION_PAGE => ''
		];
	}

	protected function initialize_settings() {
		$this->settings = \wp_parse_args( [
			Constants::SHEERID_ENABLED           => $this->object->get_meta( Constants::SHEERID_ENABLED ),
			Constants::SHEERID_PROGRAM           => $this->object->get_meta( Constants::SHEERID_PROGRAM ),
			Constants::SHEERID_ENABLE_LINK       => $this->object->get_meta( Constants::SHEERID_ENABLE_LINK ),
			Constants::SHEERID_LINK_TYPE         => $this->object->get_meta( Constants::SHEERID_LINK_TYPE ),
			Constants::SHEERID_VERIFICATION_PAGE => $this->object->get_meta( Constants::SHEERID_VERIFICATION_PAGE )
		], $this->get_default_settings() );
	}

	public function has_program() {
		return $this->get_option( Constants::SHEERID_ENABLED )
		       && $this->get_option( Constants::SHEERID_PROGRAM );
	}

	public function is_require_before_cart() {
		return \wc_string_to_bool( $this->get_option( 'require_before_cart' ) );
	}

	public function is_redirect_enabled() {
		return $this->get_option( Constants::SHEERID_LINK_TYPE ) === 'redirect';
	}

	public function is_verification_link_enabled() {
		return $this->get_option( Constants::SHEERID_ENABLE_LINK );
	}

	public function get_redirect_page_id() {
		return $this->get_option( Constants::SHEERID_VERIFICATION_PAGE );
	}

	public function get_program_id() {
		return $this->get_option( Constants::SHEERID_PROGRAM );
	}

	public function get_element_attributes( $attributes = [] ) {
		/*$url = get_page_link( $this->get_option( Constants::SHEERID_VERIFICATION_PAGE ) );
		$url = add_query_arg( [
			'verification_jwt' => JWTUtil::encode( [
				'coupon' => $this->object->get_code()
			] )
		], $url );*/

		return parent::get_element_attributes( $attributes ); // TODO: Change the autogenerated stub
	}

}