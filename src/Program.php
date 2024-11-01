<?php

namespace WooCommerce\SheerID;

use SheerID\Model\Webhook;
use WooCommerce\SheerID\Database\ProgramDataStore;

class Program extends Database\Entity {

	protected $object_type = 'program';

	protected $data = [
		'program_id'     => '',
		'mode'           => 'test',
		'webhooks'       => [],
		'success_email'  => true,
		'failure_email'  => true,
		'reminder_email' => true,
		'created_at'     => '0000-00-00 00:00:00',
		'updated_at'     => '0000-00-00 00:00:00'
	];

	public function get_datastore_id() {
		return ProgramDataStore::ID;
	}

	public function set_program_id( $value ) {
		$this->set_prop( 'program_id', $value );
	}

	public function set_success_email( $value ) {
		$this->set_bool_prop( 'success_email', $value );
	}

	public function set_failure_email( $value ) {
		$this->set_bool_prop( 'failure_email', $value );
	}

	public function set_reminder_email( $value ) {
		$this->set_bool_prop( 'reminder_email', $value );
	}

	public function set_created_at( $value ) {
		$this->set_date_prop( 'created_at', $value );
	}

	public function set_updated_at( $value ) {
		$this->set_date_prop( 'updated_at', $value );
	}

	public function set_mode( $value ) {
		$this->set_prop( 'mode', $value );
	}

	public function set_webhooks( $webhooks ) {
		$values   = [];
		$webhooks = maybe_unserialize( $webhooks );
		if ( \is_array( $webhooks ) ) {
			foreach ( $webhooks as $webhook ) {
				if ( \is_array( $webhook ) ) {
					$values[] = Webhook::constructFrom( $webhook );
				} else {
					$values[] = $webhook;
				}
			}
		}
		$this->set_prop( 'webhooks', $values );
	}

	public function get_program_id() {
		return $this->get_prop( 'program_id' );
	}

	public function get_mode() {
		return $this->get_prop( 'mode' );
	}

	/**
	 * @return bool
	 */
	public function get_success_email() {
		return $this->get_prop( 'success_email' );
	}

	/**
	 * @return bool
	 */
	public function get_failure_email() {
		return $this->get_prop( 'failure_email' );
	}

	/**
	 * @return bool
	 */
	public function get_reminder_email() {
		return $this->get_prop( 'reminder_email' );
	}

	/**
	 * @return \DateTime
	 */
	public function get_created_at() {
		return $this->get_prop( 'created_at' );
	}

	public function get_updated_at() {
		return $this->get_prop( 'updated_at' );
	}

	/**
	 * @return Webhook[]|array
	 */
	public function get_webhooks() {
		return $this->get_prop( 'webhooks' );
	}

	public function is_reminder_email_enabled() {
		return $this->get_reminder_email();
	}

	public function is_success_email_enabled() {
		return $this->get_success_email();
	}

	public function is_failure_email_enabled() {
		return $this->get_failure_email();
	}

	public function is_live() {
		return $this->get_mode() === 'live';
	}

	public function get_webhook_url() {
		$webhooks = $this->get_webhooks();
		if ( ! empty( $webhooks ) ) {
			$webhook = $webhooks[0];

			return $webhook->getCallbackUri();
		}

		return get_rest_url( null, '/wc-sheerid/v1/webhook' );
	}

	public function get_webhook_scheme() {
		return \wp_parse_url( $this->get_webhook_url() )['scheme'];
	}

	public function get_webhook_domain() {
		$webhooks = $this->get_webhooks();
		if ( ! empty( $webhooks ) ) {
			$webhook = $webhooks[0];

			$parts = \wp_parse_url( $webhook->getCallbackUri() );
		} else {
			$parts = \wp_parse_url( get_home_url() );
		}
		if ( $parts['host'] === 'localhost' && isset( $parts['port'] ) ) {
			$parts['host'] = $parts['host'] . ':' . $parts['port'];
		}

		return $parts['host'];
	}

}