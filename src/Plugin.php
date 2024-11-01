<?php

namespace WooCommerce\SheerID;

/**
 * Main plugin class that laods all functionality
 */
class Plugin {

	private $version;

	private $base_path;

	/**
	 * @var \WooCommerce\SheerID\Container\BaseContainer
	 */
	private $container;

	private static $_instance;

	private $plugin_file;

	public function __construct( $version, $file, $container ) {
		$this->version     = $version;
		$this->plugin_file = $file;
		$this->base_path   = dirname( $file ) . '/';
		$this->container   = $container;
		self::$_instance   = $this;
	}

	public function initialize() {
		$this->container->register( Plugin::class, $this );
		add_action( 'plugins_loaded', [ $this->container, 'register_services' ], 5 );
		add_action( 'woocommerce_init', [ $this->container, 'initialize_services' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->get_plugin_file() ), [ $this, 'add_links' ] );
		add_action( 'admin_init', [ $this, 'maybe_redirect_after_activation' ] );
		register_activation_hook( plugin_basename( $this->get_plugin_file() ), [ $this, 'plugin_activation' ] );
	}

	public static function container() {
		return self::$_instance->get_container();
	}

	public function set_container( $container ) {
		$this->container = $container;
	}

	public function get_container() {
		return $this->container;
	}

	public function version() {
		return $this->version;
	}

	public function get_base_path() {
		return $this->base_path;
	}

	public function get_plugin_file() {
		return $this->plugin_file;
	}

	public function add_links( $links ) {
		return $links + [
				'settings' => sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=wc-sheerid_api' ), esc_html__( 'Settings', 'sheerid-for-woocommerce' ) )
			];
	}

	public function plugin_activation() {
		$version = get_option( 'wc_sheerid_version', null );
		if ( ! $version ) {
			update_option( Constants::PLUGIN_ACTIVATION, 'yes' );
		}
	}

	public function maybe_redirect_after_activation() {
		if ( get_option( Constants::PLUGIN_ACTIVATION ) === 'yes' ) {
			delete_option( Constants::PLUGIN_ACTIVATION );
			wp_safe_redirect( admin_url( 'admin.php?page=wc-sheerid_api&wc_sheerid_activation=true' ) );
		}
	}

}