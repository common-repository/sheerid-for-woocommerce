<?php

namespace WooCommerce\SheerID;

class TemplateLoader {

	private $base_path;

	private $template_path;

	public function __construct( $base_path, $template_path ) {
		$this->base_path     = $base_path;
		$this->template_path = $template_path;
	}

	public function load_template( $template, $args = [] ) {
		\wc_get_template( $template, $args, $this->get_template_path(), $this->get_default_path() );
	}

	public function load_template_html( $template, $args = [] ) {
		return \wc_get_template_html( $template, $args, $this->get_template_path(), $this->get_default_path() );
	}

	public function get_template_path() {
		return $this->template_path;
	}

	public function get_default_path() {
		return \rtrim( $this->base_path, '\\/' ) . '/templates/';
	}

}