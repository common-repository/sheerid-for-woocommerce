<?php

namespace WooCommerce\SheerID\Emails;

use WooCommerce\SheerID\Verification;

/**
 * Email that is sent when a customer has exceeded the allowed number of document upload attempts.
 */
class MaxDocumentUploadEmail extends AbstractEmail {

	public $id = 'sheerid_upload_limit';

	public function __construct( ...$args ) {
		$this->customer_email = true;
		$this->title          = __( 'SheerID Maximum Upload Exceeded', 'sheerid-for-woocommerce' );
		$this->description    = __( 'This email is sent when the user has exceeded the maximum number of attempts for uploading documents.', 'sheerid-for-woocommerce' );
		$this->template_html  = 'emails/max-document-upload.php';
		$this->subject        = __( 'Maximum attempts reached', 'sheerid-for-woocommerce' );
		$this->heading        = __( 'You have reached the document upload limit.', 'sheerid-for-woocommerce' );

		parent::__construct( ...$args );
	}

	public function initialize() {
		add_action( 'woocommerce_sheerid_verification_status_error_notification', [ $this, 'trigger' ], 10, 2 );
	}

	/**
	 * @param int                               $id
	 * @param \WooCommerce\SheerID\Verification $verification
	 *
	 * @return void
	 */
	public function trigger( int $id, Verification $verification ) {
		try {
			$this->before_trigger( $verification );

			$this->recipient = $this->verification_details->getPersonInfo()->getEmail();
			if ( $this->local_program->is_failure_email_enabled() && $this->recipient && $this->should_send_notification() ) {
				$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
		} catch ( \Exception $e ) {
			// write error to the log
			$this->log->error( sprintf( __( 'Error processing verification success email. Reason: %1$s', 'sheerid-for-woocommerce' ), $e->getMessage() ) );
		}
	}

	public function get_content_html() {
		$last_error = $this->verification_details->getDocUploadRejectionReasons()[ $this->verification_details->getDocUploadRejectionCount() - 1 ];

		return $this->templates->load_template_html( $this->template_html, [
			'verification'         => $this->verification,
			'verification_details' => $this->verification_details,
			'current_error'        => $last_error,
			'program'              => $this->program,
			'blogname'             => $this->get_blogname(),
			'email_heading'        => $this->get_heading(),
			'additional_content'   => $this->get_additional_content(),
			'sent_to_admin'        => false,
			'plain_text'           => false,
			'email'                => $this,
		] );
	}

	/**
	 * Returns true if the notification can be sent
	 *
	 * @return bool
	 */
	private function should_send_notification() {
		// validate that the document count is greater than 2
		if ( $this->verification_details && $this->verification_details->getLastResponse()->getErrorIds() ) {
			return \in_array( 'docReviewLimitExceeded', $this->verification_details->getLastResponse()->getErrorIds() );
		}

		return false;
	}

}