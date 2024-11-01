<?php

namespace WooCommerce\SheerID\Admin\MetaBox;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\MetaBox\Traits\ProgramTrait;
use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Constants;
use WooCommerce\SheerID\Options\CouponOptions;

class MetaBoxCouponData {

	use ProgramTrait;

	private $client;

	private $assets;

	public function __construct( BaseClient $client, AssetsApi $assets ) {
		$this->client = $client;
		$this->assets = $assets;
	}

	public function initialize() {
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'woocommerce_coupon_data_tabs', [ $this, 'add_data_tabs' ] );
		add_action( 'woocommerce_coupon_data_panels', [ $this, 'render_data_panels' ], 10, 2 );
		add_action( 'woocommerce_coupon_options_save', [ $this, 'save' ], 10, 2 );
	}

	public function register_scripts() {
		$this->assets->register_script( 'wc-sheerid-coupon-data', 'assets/build/admin-coupon-data.js', [ 'wc-sheerid-admin-commons' ] );
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( in_array( $screen_id, array( 'shop_coupon', 'edit-shop_coupon' ) ) ) {
			wp_enqueue_script( 'wc-sheerid-coupon-data' );
		}
	}

	public function add_data_tabs( $tabs ) {
		$tabs['sheerid_settings'] = [
			'label'  => __( 'SheerID Settings', 'sheerid-for-woocommerce' ),
			'target' => 'sheerid_settings_coupon_data',
			'class'  => ''
		];

		return $tabs;
	}

	/**
	 * @param int        $id
	 * @param \WC_Coupon $coupon
	 *
	 * @return void
	 */
	public function render_data_panels( $id, $coupon ) {
		$options = new CouponOptions( $coupon );
		?>
        <div id="sheerid_settings_coupon_data" class="panel woocommerce_options_panel sheerid_settings_coupon_data">
            <div class="options_group">
				<?php
				woocommerce_wp_checkbox( [
					'id'          => Constants::SHEERID_ENABLED,
					'label'       => __( 'Enabled', 'sheerid-for-woocommerce' ),
					'default'     => false,
					'value'       => \wc_bool_to_string( $options->get_option( Constants::SHEERID_ENABLED ) ),
					'description' => __( 'When enabled, your customer will need to verify their identity before they can apply the coupon to their cart. ', 'sheerid-for-woocommerce' )
				] );

				woocommerce_wp_select( [
					'id'          => Constants::SHEERID_PROGRAM,
					'label'       => __( 'Sheer ID Program', 'sheerid-for-woocommerce' ),
					'options'     => $this->get_program_options(),
					'desc_tip'    => true,
					'description' => __( 'This is the program your customer must verify against in order to apply this coupon.', 'sheerid-for-woocommerce' )
				] );

				woocommerce_wp_checkbox( [
					'id'          => Constants::SHEERID_ENABLE_LINK,
					'label'       => __( 'Verification Link Enabled', 'sheerid-for-woocommerce' ),
					'default'     => true,
					'value'       => \wc_bool_to_string( $options->get_option( Constants::SHEERID_ENABLE_LINK ) ),
					'desc_tip'    => true,
					'description' => __( 'If enabled, the verification notice for the coupon will contain a link to your verification page.', 'sheerid-for-woocommerce' )
				] );

				echo '<div class="show_if_' . esc_html( Constants::SHEERID_ENABLE_LINK ) . '_checked hide_if_' . esc_html( Constants::SHEERID_ENABLE_LINK ) . '_unchecked">';

				woocommerce_wp_select( [
					'id'          => Constants::SHEERID_LINK_TYPE,
					'label'       => __( 'Click here behavior', 'sheerid-for-woocommerce' ),
					'value'       => $options->get_option( Constants::SHEERID_LINK_TYPE ),
					'default'     => 'stay',
					'options'     => [
						'stay'     => __( 'Open verification modal', 'sheerid-for-woocommerce' ),
						'redirect' => __( 'Redirect to verification page.', 'sheerid-for-woocommerce' )
					],
					'desc_tip'    => true,
					'description' => __( 'This option determines if the "click here" text opens the verification modal on your product page or if it redirects to the specified page.', 'sheerid-for-woocommerce' )
				] );

				woocommerce_wp_select( [
					'id'            => Constants::SHEERID_VERIFICATION_PAGE,
					'wrapper_class' => 'select short show_if_' . Constants::SHEERID_LINK_TYPE . '_redirect hide_if_' . Constants::SHEERID_LINK_TYPE . '_stay',
					'label'         => __( 'Verification Page', 'sheerid-for-woocommerce' ),
					'value'         => $options->get_option( Constants::SHEERID_VERIFICATION_PAGE ),
					'options'       => array_reduce( get_pages( [ 'status' => 'publish' ] ), function ( $carry, $page ) {
						$carry[ $page->ID ] = $page->post_title;

						return $carry;
					}, [] ),
					'desc_tip'      => true,
					'description'   => __( 'This is page that contains the verification that you want customers to be redirected to.', 'sheerid-for-woocommerce' )
				] );

				echo '</div>';
				?>
            </div>
        </div>
		<?php
	}

	/**
	 * @param            $coupon_id
	 * @param \WC_Coupon $coupon
	 *
	 * @return void
	 */
	public function save( $coupon_id, $coupon ) {
		$coupon->update_meta_data( Constants::SHEERID_ENABLED, isset( $_POST[ Constants::SHEERID_ENABLED ] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		$coupon->update_meta_data( Constants::SHEERID_ENABLE_LINK, isset( $_POST[ Constants::SHEERID_ENABLE_LINK ] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		// nonce verification occurs in WC_Admin_Meta_Boxes::save_meta_boxes
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ Constants::SHEERID_PROGRAM ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$coupon->update_meta_data( Constants::SHEERID_PROGRAM, \sanitize_text_field( \wp_unslash( $_POST[ Constants::SHEERID_PROGRAM ] ) ) );
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ Constants::SHEERID_VERIFICATION_PAGE ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$coupon->update_meta_data( Constants::SHEERID_VERIFICATION_PAGE, \sanitize_text_field( \wp_unslash( $_POST[ Constants::SHEERID_VERIFICATION_PAGE ] ) ) );
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ Constants::SHEERID_LINK_TYPE ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$coupon->update_meta_data( Constants::SHEERID_LINK_TYPE, \sanitize_text_field( wp_unslash( $_POST[ Constants::SHEERID_LINK_TYPE ] ) ) );
		}
		$coupon->save();
	}

}