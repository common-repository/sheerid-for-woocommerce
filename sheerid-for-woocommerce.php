<?php
/**
 * Plugin Name: SheerID for WooCommerce
 * Plugin URI: https://my.sheerid.com/
 * Description: SheerID integration for WooCommerce
 * Version: 1.0.3
 * Author: SheerID, woocommerce@sheerid.com
 * Text Domain: sheerid-for-woocommerce
 * Domain Path: /i18n/languages/
 * Tested up to: 6.6
 * Requires at least: 4.7
 * Requires PHP: 7.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 3.4
 * WC tested up to: 9.3
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

\WooCommerce\SheerID\PluginValidation::is_valid( function () {
	( new \WooCommerce\SheerID\Plugin(
		'1.0.3',
		__FILE__,
		new \WooCommerce\SheerID\Container\BaseContainer()
	) )->initialize();
} );