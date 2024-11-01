<?php

namespace WooCommerce\SheerID;

class Install {

	private $version;

	private $db_version;

	private $updates = [];

	public function __construct( $version ) {
		$this->version = $version;
	}

	public function initialize() {
		add_action( 'init', [ $this, 'process_update' ] );
	}

	public function process_update() {
		$this->db_version = get_option( 'wc_sheerid_version', null );
		// if the version option doesn't exist or the db version is less than the code version, do the update
		if ( ! $this->db_version || version_compare( $this->version, $this->db_version, '>' ) ) {
			$this->process_install();
			$this->process_updates();
			$this->update_version();
		}
	}

	private function process_updates() {
		$path = sheerid_wc_container()->get( Plugin::class )->get_base_path();
		foreach ( $this->updates as $version ) {
			$file = $path . "src/updates/update-{$version}.php";
			if ( \version_compare( $this->db_version, $version, '<' ) ) {
				if ( \file_exists( $file ) ) {
					include $file;
				}
			}
		}
	}

	private function process_install() {
		$this->create_tables();
	}

	private function create_tables() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$tables = $this->get_table_schema();
		dbDelta( $tables );
	}

	private function update_version() {
		update_option( 'wc_sheerid_version', $this->version );
	}

	private function get_table_schema() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$schema = "
		CREATE TABLE {$wpdb->prefix}sheerid_verifications (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id char(32) NOT NULL,
			verification_id char(32) NOT NULL,
			program_id char(32) NOT NULL,
			mode char(12),
			expiration bigint(20) unsigned NOT NULL,
			status char(64),
			first_name text NULL,
			last_name text NULL,
			email varchar(320),
			segment varchar(64),
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			UNIQUE KEY verification_id (verification_id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}sheerid_programs (
		    id bigint(20) NOT NULL AUTO_INCREMENT,
		    program_id char(32) NOT NULL,
		    mode char(12),
		    webhooks longtext NULL,
			success_email boolean DEFAULT true,
			failure_email boolean DEFAULT true,
			reminder_email boolean DEFAULT true,
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		    PRIMARY KEY (id),
			UNIQUE KEY program_id (program_id)
		) $collate;";

		return $schema;
	}

}