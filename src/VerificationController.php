<?php

namespace WooCommerce\SheerID;

class VerificationController {

	private $client;

	private $messages;

	public function __construct( SheerIDClient $client, MessageMap $messages ) {
		$this->client   = $client;
		$this->messages = $messages;
	}

	public function initialize() {
	}

	public function create_verification( $program_id, $page_id, $context_args = [] ) {
		$result = [];
		// make sure the program exists
		$program = $this->client->programs->retrieve( $program_id );

		if ( ! $program || is_wp_error( $program ) ) {
			throw new \Exception( esc_html__( 'Invalid program ID.', 'sheerid-for-woocommerce' ) );
		}

		// check to see if the verification already exists. If so, re-use it.
		$verification = \sheerid_wc_get_verification( [
			'program' => $program_id,
			'user_id' => WC()->session->get_customer_id(),
			'mode'    => $program->isLive() ? 'live' : 'test'
		] );

		if ( $verification ) {
			// make sure the verification actually exists in SheerID to prevent
			// a user from spoofing an entry in the local DB
			try {
				if ( ! $verification->is_valid() ) {
					throw new \Exception( 'expired verification' );
				}
				$response = $this->client->verificationDetails->retrieve( $verification->get_verification_id() );
				if ( is_wp_error( $response ) ) {
					throw new \Exception( 'invalid verification' );
				}

				// if the current time - verification time is greater than a year, the verification can't be re-used
				if ( ( time() - (int) $response->getCreated() ) > YEAR_IN_SECONDS ) {
					throw new \Exception( 'expired verification' );
				}

				if ( $response->getLastResponse()->getCurrentStep() === 'error' ) {
					throw new \Exception( 'Failed verification' );
				}
				$segment = $response->getLastResponse()->getSegment();
			} catch ( \Exception $e ) {
				$verification->delete();
				$verification = false;
			}
		}

		// if no verification exists or the existing verification is not valid, create a new one
		if ( ! $verification ) {
			// create the verification ID.
			$response = $this->client->verifications->create( [
				'programId' => $program_id,
				'metadata'  => [
					'session_id'  => (string) WC()->session->get_customer_id(),
					'customer_id' => WC()->customer ? (string) WC()->customer->get_id() : '0',
					'origin_url'  => get_permalink( $page_id ),
					'live'        => $program->isLive()
				]
			] );

			if ( is_wp_error( $response ) ) {
				throw new \Exception( esc_html( $response->get_error_message() ) );
			}

			$verification = new Verification();
			$verification->set_program_id( $program_id );
			$verification->set_user_id( WC()->session->get_customer_id() );
			$verification->set_verification_id( $response->getVerificationId() );
			$verification->set_status( $response->getCurrentStep() );
			$verification->set_mode( $program->isLive() ? 'live' : 'test' );
			$verification->set_segment( $response->getSegment() );
			$verification->save();
		}

		if ( WC()->customer ) {
			$args = array_filter( [
				'firstName' => WC()->customer->get_first_name(),
				'lastName'  => WC()->customer->get_last_name(),
				'email'     => WC()->customer->get_email()
			] );
			if ( $args ) {
				$result['view_model'] = $args;
			}
		}

		return array_merge(
			[
				'url'          => add_query_arg( [ 'verificationId' => $verification->get_verification_id() ], sprintf( Constants::VERIFICATION_URL, $program->getId() ) ),
				'program'      => $program->getId(),
				'verification' => $verification->get_verification_id(),
				'messages'     => $this->messages->get_contextual_messages( $verification, $context_args )
			],
			$result
		);
	}

}