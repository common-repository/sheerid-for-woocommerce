<?php

namespace WooCommerce\SheerID\Shortcode;

use WooCommerce\SheerID\Assets\AssetData;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\TemplateLoader;

abstract class AbstractShortcode {

	protected $id;

	/**
	 * @var \WooCommerce\SheerID\Shortcode\ShortcodeAttributes
	 */
	protected $attributes;

	protected $assets;

	protected $data;

	protected $templates;

	protected $is_active = false;

	public $enqueue_scripts = false;

	private $script_data_added = false;

	public function __construct( AssetsApi $assets, TemplateLoader $templates, AssetData $data ) {
		$this->assets    = $assets;
		$this->templates = $templates;
		$this->data      = $data;
	}

	public function is_active() {
		return $this->is_active || \wc_post_content_has_shortcode( $this->get_id() );
	}

	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * @param array $attributes
	 *
	 * @return void
	 */
	public function render_shortcode( $attributes ) {
		$this->is_active  = true;
		$this->attributes = new ShortcodeAttributes( $attributes, $this->get_shortcode_attributes() );
		$this->enqueue_scripts();
		$this->before_render();

		return $this->render();
	}

	public function enqueue_scripts() {
	}

	public function register_scripts() {
	}

	protected function before_render() {
	}

	public function add_script_data() {
	}

	protected function render() {
	}

	public function get_id() {
		return $this->id;
	}

	public function get_shortcode_attributes() {
		return [];
	}

	public function set_script_data_added( $bool ) {
		$this->script_data_added = $bool;
	}

	public function get_script_data_added() {
		return $this->script_data_added;
	}

}