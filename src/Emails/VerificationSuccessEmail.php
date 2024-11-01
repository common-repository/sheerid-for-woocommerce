<?php

namespace WooCommerce\SheerID\Emails;

use WooCommerce\SheerID\Verification;

/**
 * Email that is sent when a verification is successful.
 */
class VerificationSuccessEmail extends AbstractEmail {

	public $id = 'sheerid_verification_success';

	public function __construct( ...$args ) {
		$this->customer_email = true;
		$this->title          = __( 'SheerID Verification Success', 'sheerid-for-woocommerce' );
		$this->description    = __( 'The verification success email is sent when the user has successfully completed their identity verification.', 'sheerid-for-woocommerce' );
		$this->template_html  = 'emails/verification-success.php';
		$this->subject        = __( 'Verification success', 'sheerid-for-woocommerce' );
		$this->heading        = __( 'Your identity has been verified.', 'sheerid-for-woocommerce' );

		parent::__construct( ...$args );
	}

	public function initialize() {
		add_action( 'woocommerce_sheerid_verification_status_success_notification', [ $this, 'trigger' ], 10, 2 );
	}

	/**
	 * @param \WooCommerce\SheerID\Verification $verification
	 *
	 * @return void
	 */
	public function trigger( int $id, Verification $verification ) {
		try {
			$this->before_trigger( $verification );

			$this->recipient = $this->verification_details->getPersonInfo()->getEmail();
			if ( $this->local_program && $this->local_program->is_success_email_enabled() && $this->recipient ) {
				$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
		} catch ( \Exception $e ) {
			// write error to the log
			$this->log->error( sprintf( __( 'Error processing verification success email. Reason: %1$s', 'sheerid-for-woocommerce' ), $e->getMessage() ) );
		}
	}

	public function get_content_html() {
		return $this->templates->load_template_html( $this->template_html, [
			'verification'       => $this->verification,
			'program'            => $this->program,
			'blogname'           => $this->get_blogname(),
			'email_heading'      => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin'      => false,
			'plain_text'         => false,
			'email'              => $this,
		] );
	}

}