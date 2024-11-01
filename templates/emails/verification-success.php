<?php
/**
 *
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

    <div>
        <h3><?php printf( esc_html__( 'We have successfully confirmed your %1$s status', 'sheerid-for-woocommerce' ), esc_html( $program->getName() ) ) ?></h3>
        <p><?php esc_html_e( 'Congratulations!', 'sheerid-for-woocommerce' ) ?></p>
    </div>

<?php
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}
?>
    <div>
        <p><?php esc_html_e( 'Kind Regards,', 'sheerid-for-woocommerce' ) ?></p>
        <p><?php echo esc_html( $blogname ) ?></p>
    </div>
<?php
/*
* @hooked WC_Emails::email_footer() Output the email footer
*/
do_action( 'woocommerce_email_footer', $email );
