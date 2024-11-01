<?php

/**
 * @var \SheerID\Model\Verification\VerificationDetails $verification_details
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

    <div>
		<?php printf( esc_html__( 'Hi %1$s', 'sheerid-for-woocommerce' ), esc_html( $verification_details->getPersonInfo()->getFirstName() ) ) ?>
        <div style="margin-top: 20px; margin-bottom: 20px">
			<?php printf( esc_html__( 'Thank you for starting the %1$s verification process. Let\'s get you across the finish line!', 'sheerid-for-woocommerce' ), esc_html( $program->getName() ) ) ?>
            <div style="margin-top: 10px;">
                <a target="_blank" href="<?php echo esc_attr( esc_url( $verification_url ) ) ?>"><?php esc_html_e( 'Click here to finish verifying', 'sheerid-for-woocommerce' ) ?></a>
            </div>
        </div>
    </div>

<?php
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}
?>
    <div style="margin-top: 20px">
        <p><?php esc_html_e( 'Kind Regards,', 'sheerid-for-woocommerce' ) ?></p>
        <p><?php echo esc_html( $blogname ) ?></p>
    </div>
<?php
/*
* @hooked WC_Emails::email_footer() Output the email footer
*/
do_action( 'woocommerce_email_footer', $email );
