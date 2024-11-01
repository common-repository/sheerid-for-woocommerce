<?php

namespace WooCommerce\SheerID;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Database\VerificationDataStore;

class CustomerController {

	private $client;

	public function __construct( BaseClient $client ) {
		$this->client = $client;
	}

	public function initialize() {
		add_action( 'woocommerce_created_customer', [ $this, 'handle_customer_created' ] );
	}

	/**
	 * @param int $customer_id
	 *
	 * @return void
	 */
	public function handle_customer_created( $customer_id ) {
		// update the customer's verification entries if they exist so they contain the new customer_id rather
		// than the session_id
		$session_id = WC()->session->get_customer_id();
		if ( $customer_id !== $session_id ) {
			// update the verifications so they have the correct user_id
			$verifications = sheerid_wc_get_verifications( [ 'user_id' => $session_id ] );
			if ( $verifications ) {
				// update each verification stored within SheerID with new customer_id
				foreach ( $verifications as $verification ) {
					$this->client->verifications->updateMetadata( $verification->get_verification_id(),
						[
							'session_id'  => $session_id,
							'customer_id' => $customer_id
						]
					);
				}
			}

			// update all verifications where user_id = $session_id
			\WC_Data_Store::load( VerificationDataStore::ID )->update_user_id( $customer_id, $session_id );
		}
	}

}