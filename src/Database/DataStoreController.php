<?php

namespace WooCommerce\SheerID\Database;

class DataStoreController {

	public function __construct() {
	}

	public function initialize() {
		$this->add_tables_to_database_reference();
		add_filter( 'woocommerce_data_stores', [ $this, 'add_data_stores' ] );
	}

	private function add_tables_to_database_reference() {
		global $wpdb;
		foreach ( $this->get_table_names() as $name ) {
			$wpdb->{$name} = $wpdb->prefix . $name;
		}
	}

	public function add_data_stores( $stores ) {
		return \array_merge( $stores, $this->get_data_stores() );
	}

	private function get_data_stores() {
		return [
			VerificationDataStore::ID => VerificationDataStore::class,
			ProgramDataStore::ID      => ProgramDataStore::class
		];
	}

	private function get_table_names() {
		return [
			'sheerid_verifications',
			'sheerid_programs'
		];
	}

}