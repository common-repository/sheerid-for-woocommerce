<?php

namespace WooCommerce\SheerID\Emails;

use WooCommerce\SheerID\Verification;

/**
 * Email that is sent when customer's need a reminder to upload their verification documents.
 */
class DocumentUploadReminderEmail extends AbstractEmail {

	public $id = 'sheerid_document_reminder';

	public function __construct( ...$args ) {
		$this->customer_email = true;
		$this->title          = __( 'SheerID Document Upload Reminder', 'sheerid-for-woocommerce' );
		$this->description    = __( 'Document upload reminder emails are sent when a customer takes more than 3 minutes to upload their verification documents.', 'sheerid-for-woocommerce' );
		$this->template_html  = 'emails/document-upload-reminder.php';
		$this->subject        = __( 'Verification documents required', 'sheerid-for-woocommerce' );
		$this->heading        = __( 'Your verification is almost complete.', 'sheerid-for-woocommerce' );

		parent::__construct( ...$args );
	}

	public function initialize() {
		add_action( 'woocommerce_sheerid_verification_reminder_notification', [ $this, 'trigger' ], 10, 2 );
	}

	public function trigger( int $id, Verification $verification ) {
		try {
			$this->before_trigger( $verification );

			$this->recipient = $this->verification_details->getPersonInfo()->getEmail();
			if ( $this->local_program->is_reminder_email_enabled() && $this->recipient ) {
				$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				$this->log->info( sprintf( 'Verification upload reminder email sent.' ) );
			}
		} catch ( \Exception $e ) {
			$this->log->error( sprintf( __( 'Error processing verification document upload reminder. Reason: %1$s', 'sheerid-for-woocommerce' ), $e->getMessage() ) );
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
			'verification_url'     => $this->verification_details->getPersonInfo()->getMetadata()->origin_url
		] );
	}

}