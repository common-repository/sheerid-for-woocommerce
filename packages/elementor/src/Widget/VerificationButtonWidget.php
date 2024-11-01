<?php

namespace WooCommerce\SheerID\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Includes\Widgets\Traits\Button_Trait;
use WooCommerce\SheerID\Constants;
use WooCommerce\SheerID\Utils\JavascriptData;

class VerificationButtonWidget extends \Elementor\Widget_Base {

	use Button_Trait;

	public function register_controls() {
		$this->start_controls_section(
			'section_button',
			[
				'label' => esc_html__( 'Button', 'sheerid-for-woocommerce' ),
			]
		);

		$this->register_button_controls();

		$this->register_button_content_controls( [
			'button_default_text' => __( 'Verify Identity', 'sheerid-for-woocommerce' )
		] );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Button', 'sheerid-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_button_style_controls();

		$this->end_controls_section();
	}

	public function get_name() {
		return 'sheerid_verification_button';
	}

	public function get_title() {
		return esc_html__( 'SheerID Verification Button', 'sheerid-for-woocommerce' );
	}

	public function get_keywords() {
		return [ 'sheerid', 'verification' ];
	}

	public function get_icon() {
		return 'eicon-button';
	}


	private function register_button_controls() {
		$args = [
			'section_condition' => []
		];

		$this->add_control( 'program', [
			'label'             => esc_html__( 'Program ID', 'sheerid-for-woocommerce' ),
			'type'              => Controls_Manager::TEXT,
			'default'           => '',
			'section_condition' => $args['section_condition']
		] );

		$this->add_control( 'text_loading', [
			'label'             => esc_html__( 'Loading Text', 'sheerid-for-woocommerce' ),
			'type'              => Controls_Manager::TEXT,
			'default'           => esc_html__( 'Processing...', 'sheerid-for-woocommerce' ),
			'section_condition' => $args['section_condition']
		] );
	}

	public function render() {
		$this->add_render_attribute( 'button', 'data-sheerid', wc_esc_json( wp_json_encode( JavascriptData::get_verification_data( [
			'program' => $this->get_settings_for_display( 'program' ),
			'text'    => [
				'loading' => $this->get_settings_for_display( 'text_loading' ),
				'label'   => $this->get_settings_for_display( 'text' )
			]
		] ) ) ) );
		$this->add_render_attribute( 'button', 'class', 'wcSheerIDButton' );
		$this->render_button();
	}

	public function get_script_depends() {
		return [ 'wc-sheerid-elementor-button' ];
	}

	public function get_style_depends() {
		return [ Constants::SHEER_ID_STYLES ];
	}

}