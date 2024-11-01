<?php
?>
<script type="text/template" id="tmpl-sheerid_activation_modal">
    <div class="wc-backbone-modal wc-sheerid-modal">
        <div class="wc-backbone-modal-content">
            <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header sheerid-activation-header">
                    <div class="wc-sheerid-logo">
                        <img src="<?php echo esc_url( $assets->assets_url( 'assets/img/sheerID.svg' ) ) ?>"/>
                        <span><?php esc_html_e( 'by Payment Plugins', 'sheerid-for-woocommerce' ) ?></span>
                    </div>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text">Close modal panel</span>
                    </button>
                </header>
                <article>
                    <div class="wc-sheerid-modal-content">
                        <div class="trial-period">
							<?php esc_html_e( 'Start your 28 day free trial', 'sheerid-for-woocommerce' ) ?>
                        </div>
                        <div class="modal-content-row">
                            <h5><?php esc_html_e( 'Welcome to SheerID for Woocommerce', 'sheerid-for-woocommerce' ); ?></h5>
                        </div>
                        <div class="modal-content-row">
                            <p><?php esc_html_e( 'To begin verifying customers, first connect the plugin to a SheerID account in 3 easy steps. ', 'sheerid-for-woocommerce' ) ?></p>
                        </div>
                        <div class="modal-content-row">
                            <ol>
                                <li><?php esc_html_e( 'Create an account with SheerID', 'sheerid-for-woocommerce' ) ?></li>
                                <li><?php esc_html_e( 'Confirm your email address and set your password', 'sheerid-for-woocommerce' ) ?></li>
                                <li><?php esc_html_e( 'Return here, head to the API Settings tab, and click Connect ', 'sheerid-for-woocommerce' ) ?></li>
                            </ol>
                        </div>
                        <div class="modal-content-row">
                            <a class="account-button" href="https://my.sheerid.com/auth/oem-registration" target="_blank"><?php esc_html_e( 'Create your account', 'sheerid-for-woocommerce' ) ?></a>
                        </div>
                        <div class="modal-content-row wc-sheerid-have-account">
                            <p><?php esc_html_e( 'Already have a SheerID account?', 'sheerid-for-woocommerce' ) ?></p>
                            <p><?php echo wp_kses( '<u>' . __( 'Go to the API Settings tab', 'sheerid-for-woocommerce' ) . '</u>', [ 'u' => [] ] ) . ' ' . esc_html__( 'and click "Connect" to link your account.', 'sheerid-for-woocommerce' ) ?></p>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </div>
    <div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
