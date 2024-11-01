<?php

namespace WooCommerce\SheerID;

class PluginValidation {

	/**
	 * Validates that the plugin can be loaded.
	 *
	 * @return void
	 */
	public static function is_valid( $callback = null ) {
		if ( $callback ) {
			$callback();
		}
	}

}