<?php

namespace WooCommerce\SheerID\Admin\Settings;

class AbstractSettings extends \WC_Settings_API {

	protected $title;

	protected $is_selected = false;

	public function __construct() {
		$this->init_form_fields();
		$this->init_settings();
		$this->initialize();
	}

	protected function initialize() {
	}

	public function get_id() {
		return $this->id;
	}

	public function get_title() {
		return $this->title;
	}

	public function render_options( $tabs = [] ) {
		global $sheerid_page;
		include_once dirname( __DIR__ ) . '/Views/html-admin-settings.php';
	}

	public function supports_save_settings() {
		return true;
	}

	public function set_is_selected( $bool ) {
		$this->is_selected = $bool;
	}

}