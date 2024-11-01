<?php
/**
 *
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

    <div>
        <h3><?php printf( esc_html__( 'An update on verifying your eligibility', 'sheerid-for-woocommerce' ), esc_html( $program->getName() ) ) ?></h3>
        <p><?php esc_html_e( 'Thank you for uploading your documentation for confirmation. Unfortunately we were unable to confirm your status.', 'sheerid-for-woocommerce' ) ?></p>
        <div>
            <ul>
				<?php foreach ( $rejection_reasons as $code ): ?>
                    <li><p><?php echo esc_html( $code ) ?></p></li>
				<?php endforeach; ?>
            </ul>
        </div>
        <p><?php esc_html_e( 'You may upload another scan of one of the acceptable documents to complete the process by clicking the button below.', 'sheerid-for-woocommerce' ) ?></p>
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
