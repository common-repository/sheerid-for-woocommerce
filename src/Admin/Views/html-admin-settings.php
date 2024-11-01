<?php

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce sheerid-settings-container">
    <form method="post" enctype="multipart/form-data">
        <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php

			foreach ( $tabs as $slug => $label ) {
				echo '<a href="' . esc_html( admin_url( 'admin.php?page=wc-' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $sheerid_page === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
			}

			?>
            <a class="nav-tab documentation" target="_blank" href="https://docs.paymentplugins.com/wc-sheerid/config"><?php esc_html_e( 'Documentation', 'sheerid-for-woocommerce' ) ?></a>
        </nav>
        <div>
			<?php $this->admin_options() ?>
        </div>
		<?php if ( $this->supports_save_settings() ): ?>
            <p class="submit">
                <button name="sheerid_save" type="submit" class="button-primary woocommerce-save-button" value="<?php echo esc_attr( $this->get_id() ) ?>">
					<?php esc_html_e( 'Save changes', 'sheerid-for-woocommerce' ) ?>
                </button>
				<?php wp_nonce_field( 'woocommerce-sheerid-settings' ); ?>
            </p>
		<?php endif; ?>
    </form>
</div>
