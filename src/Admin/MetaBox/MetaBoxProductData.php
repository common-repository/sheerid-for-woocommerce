<?php

namespace WooCommerce\SheerID\Admin\MetaBox;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\MetaBox\Traits\ProgramTrait;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Options\ProductOptions;

class MetaBoxProductData {

	use ProgramTrait;

	private $client;

	private $assets;

	public function __construct( BaseClient $client, AssetsApi $assets ) {
		$this->client = $client;
		$this->assets = $assets;
	}

	public function initialize() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_data_tabs' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'render' ] );
		add_action( 'woocommerce_admin_process_product_object', [ $this, 'save' ] );

		$this->register_scripts();
	}

	private function register_scripts() {
		$this->assets->register_script( 'wc-sheerid-metabox-product', 'assets/build/meta-boxes-product.js', [ 'wc-sheerid-admin-commons' ] );
	}

	public function enqueue_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( \in_array( $screen_id, [ 'product', 'edit-product' ] ) ) {
			wp_enqueue_script( 'wc-sheerid-metabox-product' );
		}
	}

	public function add_data_tabs( $tabs ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$tabs['wc_sheerid'] = [
				'label'    => __( 'SheerID Settings', 'sheerid-for-woocommerce' ),
				'target'   => 'wc_sheerid_product_data',
				'class'    => [ 'hide_if_external' ],
				'priority' => 102
			];
		}

		return $tabs;
	}

	public function render() {
		global $product_object;
		$settings = new ProductOptions( $product_object );
		?>
        <div id="wc_sheerid_product_data" class="panel woocommerce_options_panel">
            <p>
				<?php esc_html_e( 'In this section you can configure your product to require identity verification before it can be purchased.', 'sheerid-for-woocommerce' ); ?>
            </p>
            <div class="options_group">
				<?php
				woocommerce_wp_select( [
					'id'          => 'sheerid[program]',
					'label'       => __( 'Program', 'sheerid-for-woocommerce' ),
					'value'       => $settings->get_option( 'program' ),
					'options'     => array_merge( [
						'none' => __( 'None', 'sheerid-for-woocommerce' )
					], $this->get_program_options() ),
					'desc_tip'    => true,
					'description' => __( 'If selected, your customer will need to verify against the program in order to purchase this product.' )
				] );

				?>
            </div>
            <div class="options_group">
				<?php

				woocommerce_wp_checkbox( [
					'id'          => 'sheerid[require_before_cart]',
					'label'       => __( 'Require before add to cart', 'sheerid-for-woocommerce' ),
					'value'       => $settings->get_option( 'require_before_cart' ),
					'desc_tip'    => true,
					'description' => __( 'If enabled, the customer will have to verify their identity before the product can be added to their cart.', 'sheerid-for-woocommerce' )
				] );

				echo '<div class="hide_if_not_require_before_cart">';

				woocommerce_wp_checkbox( [
					'id'          => 'sheerid[click_here]',
					'label'       => __( 'Add click here', 'sheerid-for-woocommerce' ),
					'value'       => $settings->get_option( 'click_here' ),
					'desc_tip'    => true,
					'description' => __( 'If enabled, a "click here" link will be included in the error notice if a customer is not verified and attempts to add the item to the cart.', 'sheerid-for-woocommerce' )
				] );

				echo '<div class="hide_if_not_click_here">';
				woocommerce_wp_select( [
					'id'          => 'sheerid[click_here_behavior]',
					'label'       => __( 'Click here behavior', 'sheerid-for-woocommerce' ),
					'value'       => $settings->get_option( 'click_here_behavior' ),
					'options'     => [
						'stay'     => __( 'Open verification modal', 'sheerid-for-woocommerce' ),
						'redirect' => __( 'Redirect to verification page.', 'sheerid-for-woocommerce' )
					],
					'desc_tip'    => true,
					'description' => __( 'This option determines if the "click here" text opens the verification modal on your product page or if it redirects to the specified page.', 'sheerid-for-woocommerce' )
				] );

				echo '<div class="show_if_click_here_behavior_redirect">';
				woocommerce_wp_select( [
					'id'          => 'sheerid[redirect_page]',
					'label'       => __( 'Verification page', 'sheerid-for-woocommerce' ),
					'value'       => $settings->get_option( 'redirect_page' ),
					'options'     => array_reduce( get_pages( [ 'status' => 'publish' ] ), function ( $carry, $page ) {
						$carry[ $page->ID ] = $page->post_title;

						return $carry;
					}, [] ),
					'desc_tip'    => true,
					'description' => __( 'This is the page that the "click here" link will redirect to.', 'sheerid-for-woocommerce' )
				] );

				echo '</div>';

				echo '</div>';

				echo '</div>';
				?>
            </div>
        </div>
		<?php
	}

	public function save( \WC_Product $product ) {
		if ( isset( $_POST['sheerid'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			// build the options
			$values = [];

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			foreach ( wc_clean( wp_unslash( $_POST['sheerid'] ) ) as $key => $value ) {
				$values[ $key ] = $value;
			}
			$product->update_meta_data( '_sheerid_options', $values );
		}
	}

}