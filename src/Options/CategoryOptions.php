<?php

namespace WooCommerce\SheerID\Options;

class CategoryOptions extends ProductOptions {

	private $term_id;

	public function __construct( $term ) {
		if ( is_object( $term ) ) {
			$this->term_id = $term->term_id;
		} else {
			$this->term_id = $term;
		}
	}

	protected function get_object( $id ) {
		// return category
	}

	public function initialize_settings() {
		$this->settings = get_term_meta( $this->term_id, '_sheerid_options', true );
		if ( ! is_array( $this->settings ) ) {
			$this->settings = $this->get_default_settings();
		}
	}

	public function save() {
		update_term_meta( $this->term_id, '_sheerid_options', $this->settings );
	}

}