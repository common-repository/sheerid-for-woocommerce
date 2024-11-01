<?php

namespace WooCommerce\SheerID\Emails;

use WooCommerce\SheerID\Utils\GeneralUtils;
use WooCommerce\SheerID\Verification;

class VerificationReminderEmail extends AbstractEmail {

	public $id = 'sheerid_verification_reminder';

	public function __construct( ...$args ) {
		$this->customer_email = true;
		$this->title          = __( 'SheerID Verification Reminder', 'sheerid-for-woocommerce' );
		$this->description    = __( 'This is a reminder email that can be sent by the Administrator via the Verifications page.', 'sheerid-for-woocommerce' );
		$this->template_html  = 'emails/verification-reminder.php';
		$this->subject        = __( 'Verification Reminder', 'sheerid-for-woocommerce' );
		$this->heading        = __( 'Please complete your verification.', 'sheerid-for-woocommerce' );

		parent::__construct( ...$args );
	}

	public function trigger( Verification $verification ) {
		try {
			$this->before_trigger( $verification );
			$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

			return true;
		} catch ( \Exception $e ) {
			// write error to the log
			$this->last_error = sprintf( __( 'Error processing verification success email. Reason: %1$s', 'sheerid-for-woocommerce' ), $e->getMessage() );
			$this->log->error( $this->last_error );

			return false;
		}
	}

	public function get_content_html() {
		return $this->templates->load_template_html( $this->template_html, [
			'verification'         => $this->verification,
			'verification_details' => $this->verification_details,
			'program'              => $this->program,
			'blogname'             => $this->get_blogname(),
			'email_heading'        => $this->get_heading(),
			'additional_content'   => $this->get_additional_content(),
			'sent_to_admin'        => false,
			'plain_text'           => false,
			'email'                => $this,
			'verification_url'     => GeneralUtils::get_verification_url( $this->verification_details->getPersonInfo()->getMetadata()->origin_url )
		] );
	}

}