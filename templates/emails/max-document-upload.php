<?php
/**
 *
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

    <div>
        <h3><?php esc_html_e( 'Thank you for uploading your documentation for confirmation. Unfortunately we were unable to confirm your status.', 'sheerid-for-woocommerce' ) ?></h3>
        <p><?php esc_html_e( 'During the review process it was determined:', 'sheerid-for-woocommerce' ); ?></p>
        <div>
            <ul>
				<?php foreach ( $current_error as $code ): ?>
                    <li><p><?php echo esc_html( $code ) ?></p></li>
				<?php endforeach; ?>
            </ul>
        </div>
        <p><?php esc_html_e( 'You have reached the maximum number of attempts for submitting documentation.', 'sheerid-for-woocommerce' ) ?></p>
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
