<?php

namespace WooCommerce\SheerID\Admin;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\MetaBox\Traits\ProgramTrait;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Options\CategoryOptions;

class AdminTaxonomies {

	use ProgramTrait;

	private $client;

	private $assets;

	public function __construct( BaseClient $client, AssetsApi $assets ) {
		$this->client = $client;
		$this->assets = $assets;
	}

	public function initialize() {
		add_action( 'product_cat_edit_form_fields', [ $this, 'edit_category_fields' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'created_term', [ $this, 'save' ] );
		add_action( 'edit_term', [ $this, 'save' ] );
		$this->register_scripts();
	}

	private function register_scripts() {
		$this->assets->register_script(
			'wc-sheerid-category-settings',
			'assets/build/category-settings.js',
			[ 'wc-sheerid-admin-commons' ]
		);
	}

	public function enqueue_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( \in_array( $screen_id, [ 'edit-product_cat' ] ) ) {
			wp_enqueue_script( 'wc-sheerid-category-settings' );
		}
	}

	public function edit_category_fields( $term ) {
		$settings = new CategoryOptions( $term );
		?>
        <style>
            span.description {
                display: block !important;
            }
        </style>
		<?php
		?>
        <tr class="form-field">
            <th scope="row"><label><?php esc_html_e( 'SheerID Program', 'sheerid-for-woocommerce' ) ?></label></th>
            <td>
				<?php
				woocommerce_wp_select( [
					'id'          => 'sheerid[program]',
					'label'       => '',
					'value'       => $settings->get_option( 'program' ),
					'options'     => array_merge( [
						'none' => __( 'None', 'sheerid-for-woocommerce' )
					], $this->get_program_options() ),
					'description' => __( 'If selected, your customer will need to verify against the program in order to purchase products in this category.' )
				] );
				?>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label><?php esc_html_e( 'Require before add to cart', 'sheerid-for-woocommerce' ) ?></label></th>
            <td>
				<?php
				woocommerce_wp_checkbox( [
					'id'          => 'sheerid[require_before_cart]',
					'label'       => '',
					'value'       => $settings->get_option( 'require_before_cart' ),
					'description' => __( 'If enabled, the customer will have to verify their identity before the product can be added to their cart.', 'sheerid-for-woocommerce' )
				] );
				?>
            </td>
        </tr>
        <tr class="form-field hide_if_not_require_before_cart">
            <th scope="row"><label><?php esc_html_e( 'Add click here', 'sheerid-for-woocommerce' ) ?></label></th>
            <td>
				<?php
				woocommerce_wp_checkbox( [
					'id'          => 'sheerid[click_here]',
					'label'       => '',
					'value'       => $settings->get_option( 'click_here' ),
					'description' => __( 'If enabled, a "click here" link will be included in the error notice if a customer is not verified and attempts to add the item to the cart.', 'sheerid-for-woocommerce' )
				] );
				?>
            </td>
        </tr>
        <tr class="form-field hide_if_not_click_here">
            <th scope="row"><label><?php esc_html_e( 'Click here behavior', 'sheerid-for-woocommerce' ) ?></label></th>
            <td>
				<?php
				woocommerce_wp_select( [
					'id'          => 'sheerid[click_here_behavior]',
					'label'       => '',
					'value'       => $settings->get_option( 'click_here_behavior' ),
					'options'     => [
						'stay'     => __( 'Open verification modal', 'sheerid-for-woocommerce' ),
						'redirect' => __( 'Redirect to verification page.', 'sheerid-for-woocommerce' )
					],
					'description' => __( 'This option determines if the "click here" text opens the verification modal on your product page or if it redirects to the specified page.', 'sheerid-for-woocommerce' )
				] );
				?>
            </td>
        </tr>
        <tr class="form-field show_if_click_here_behavior_redirect hide_if_click_here_behavior_stay">
            <th scope="row"><label><?php esc_html_e( 'Verification page', 'sheerid-for-woocommerce' ) ?></label></th>
            <td>
				<?php
				woocommerce_wp_select( [
					'id'          => 'sheerid[redirect_page]',
					'label'       => '',
					'value'       => $settings->get_option( 'redirect_page' ),
					'options'     => array_reduce( get_pages( [ 'status' => 'publish' ] ), function ( $carry, $page ) {
						$carry[ $page->ID ] = $page->post_title;

						return $carry;
					}, [] ),
					'description' => __( 'This is the page that the "click here" link will redirect to.', 'sheerid-for-woocommerce' )
				] );
				?>
            </td>
        </tr>
		<?php
	}

	public function save( $term_id ) {
		if ( isset( $_POST['sheerid'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$settings = new CategoryOptions( $term_id );

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			foreach ( wc_clean( wp_unslash( $_POST['sheerid'] ) ) as $key => $value ) {
				$settings->update_option( $key, $value );
			}
			$settings->save();
		}
	}

}