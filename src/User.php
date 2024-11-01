<?php

namespace WooCommerce\SheerID;

class User {

	private $wp_user;

	private $session;

	private $customer;

	private $data;

	protected $data_store;

	private $session_keys = [
		'program'
	];

	public function __construct( $id ) {
	}

	private function is_logged_in() {
		return $this->user->ID > 0;
	}

	public function set_program_verification( $verification, $program ) {
		$data                   = $this->data[ $program ] ?? [ 'verification' => '' ];
		$data['verification']   = $verification;
		$this->data[ $program ] = $data;
	}

	public function add_permission( $permission ) {
	}

	public function remove_permission( $permission ) {
	}

	public function save() {
		$this->session->set( 'sheerid_user', $this->data );
	}

	private function get_defaults() {
		return [];
	}

}