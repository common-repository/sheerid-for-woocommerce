<?php

namespace WooCommerce\SheerID;

use WooCommerce\SheerID\Database\Entity;
use WooCommerce\SheerID\Database\VerificationDataStore;

class Verification extends Entity {

	protected $object_type = 'verification';

	protected $id = 0;

	protected $data = [
		'user_id'         => 0,
		'verification_id' => '',
		'program_id'      => '',
		'mode'            => 'test',
		'expiration'      => 0,
		'status'          => 'pending',
		'first_name'      => '',
		'last_name'       => '',
		'email'           => '',
		'segment'         => '',
		'created_at'      => '0000-00-00 00:00:00',
		'updated_at'      => '0000-00-00 00:00:00'
	];

	private $status_transition = false;

	public function get_datastore_id() {
		return VerificationDataStore::ID;
	}

	public function save() {
		parent::save();
		if ( $this->status_transition ) {
			do_action( 'woocommerce_sheerid_verification_status_' . $this->status_transition['to'], $this->get_id(), $this );
			do_action( 'woocommerce_sheerid_verification_status_' . $this->status_transition['from'] . '_to_' . $this->status_transition['to'], $this->get_id(), $this );
			$this->status_transition = false;
		}
	}

	public function set_user_id( $value ) {
		$this->set_prop( 'user_id', $value );
	}

	public function set_verification_id( $value ) {
		$this->set_prop( 'verification_id', $value );
	}

	public function set_program_id( $value ) {
		$this->set_prop( 'program_id', $value );
	}

	public function set_mode( $value ) {
		$this->set_prop( 'mode', $value );
	}

	public function set_expiration( $value ) {
		$this->set_prop( 'expiration', $value );
	}

	public function set_status( $value ) {
		$status = $this->transform_status( $value );
		if ( $status !== $this->get_status() && $this->object_read ) {
			$this->status_transition = [
				'from' => $this->get_status(),
				'to'   => $status
			];
		}
		$this->set_prop( 'status', $status );
	}

	public function set_created_at( $value ) {
		$this->set_date_prop( 'created_at', $value );
	}

	public function set_updated_at( $value ) {
		$this->set_date_prop( 'updated_at', $value );
	}

	public function set_first_name( $value ) {
		$this->set_prop( 'first_name', $value );
	}

	public function set_last_name( $value ) {
		$this->set_prop( 'last_name', $value );
	}

	public function set_email( $value ) {
		$this->set_prop( 'email', $value );
	}

	public function set_segment( $value ) {
		$this->set_prop( 'segment', $value );
	}

	public function get_user_id() {
		return $this->get_prop( 'user_id' );
	}

	public function get_program_id() {
		return $this->get_prop( 'program_id' );
	}

	public function get_mode() {
		return $this->get_prop( 'mode' );
	}

	public function get_verification_id() {
		return $this->get_prop( 'verification_id' );
	}

	public function get_expiration() {
		return $this->get_prop( 'expiration' );
	}

	public function get_status() {
		return $this->get_prop( 'status' );
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

	public function get_first_name() {
		return $this->get_prop( 'first_name' );
	}

	public function get_last_name() {
		return $this->get_prop( 'last_name' );
	}

	public function get_email() {
		return $this->get_prop( 'email' );
	}

	public function get_segment() {
		return $this->get_prop( 'segment' );
	}

	public function is_valid() {
		if ( $this->get_expiration() ) {
			if ( $this->get_expiration() < time() ) {
				return false;
			}
		}

		return ! $this->is_error();
	}

	public function is_success() {
		return $this->get_status() === 'success';
	}

	public function is_error() {
		return $this->get_status() === 'error';
	}

	public function update_status( $status ) {
		if ( ! $this->get_id() ) {
			return;
		}
		if ( $status !== $this->get_status() ) {
			$this->set_status( $status );
			$this->save();
		}
	}

	public function is_live() {
		return $this->get_mode() === 'live';
	}

	private function transform_status( $status ) {
		preg_match( '/^collect(?=[\w]+)/', $status, $matches );
		if ( $matches ) {
			$status = 'collectInfo';
		}

		return $status;
	}

	/**
	 * @param \SheerID\Model\Verification\VerificationDetails $verification_details
	 *
	 * @return void
	 */
	public function sync_from_sheerid_verification( $verification_details ) {
		if ( $verification_details->getPersonInfo() ) {
			$this->set_first_name( $verification_details->getPersonInfo()->getFirstName() );
			$this->set_last_name( $verification_details->getPersonInfo()->getLastName() );
			$this->set_email( $verification_details->getPersonInfo()->getEmail() );
		}
		$this->set_program_id( $verification_details->getProgramId() );
		$this->set_status( $verification_details->getLastResponse()->getCurrentStep() );
		$this->save();
	}

}