<?php

namespace WooCommerce\SheerID\Emails;

use SheerID\Model\Verification\VerificationDetails;
use WooCommerce\SheerID\Verification;

/**
 * Email that is sent when a verification fails.
 */
class VerificationFailedEmail extends AbstractEmail {

	public $id = 'sheerid_verification_failed';

	public function __construct( ...$args ) {
		$this->customer_email = true;
		$this->title          = __( 'SheerID Verification Failed', 'sheerid-for-woocommerce' );
		$this->description    = __( 'This email is sent when the user\'s verification attempt is unsuccessful.', 'sheerid-for-woocommerce' );
		$this->template_html  = 'emails/verification-failed.php';
		$this->subject        = __( 'Verification failed', 'sheerid-for-woocommerce' );
		$this->heading        = __( 'We could not verify your identity.', 'sheerid-for-woocommerce' );

		parent::__construct( ...$args );
	}

	public function initialize() {
		add_action( 'woocommerce_sheerid_verification_status_error_notification', [ $this, 'trigger' ], 10, 2 );
		add_action( 'woocommerce_sheerid_verification_upload_failed_notification', [ $this, 'trigger' ], 10, 3 );
	}

	/**
	 * @param int                               $id
	 * @param \WooCommerce\SheerID\Verification $verification
	 *
	 * @return void
	 */
	public function trigger( int $id, Verification $verification, VerificationDetails $verification_details = null ) {
		$this->verification_details = $verification_details;

		try {
			$this->before_trigger( $verification );

			$this->recipient = $this->verification_details->getPersonInfo()->getEmail();
			if ( $this->local_program && $this->local_program->is_failure_email_enabled() && $this->recipient && $this->should_send_notification() ) {
				$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
		} catch ( \Exception $e ) {
			// write error to the log
			$this->log->error( sprintf( __( 'Error processing verification success email. Reason: %1$s', 'sheerid-for-woocommerce' ), $e->getMessage() ) );
		}
	}

	public function get_content_html() {
		$last_response     = $this->verification_details->getLastResponse();
		$rejection_reasons = $last_response->getRejectionReasons();

		return $this->templates->load_template_html( $this->template_html, [
			'verification'         => $this->verification,
			'verification_details' => $this->verification_details,
			'rejection_reasons'    => $rejection_reasons,
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
		return $this->verification_details && $this->verification_details->getDocUploadRejectionCount() < 3;
	}

}