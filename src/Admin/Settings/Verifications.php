<?php

namespace WooCommerce\SheerID\Admin\Settings;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\Tables\VerificationsTable;

class Verifications extends AbstractSettings {

	public $id = 'sheerid_verifications';

	private $client;

	/**
	 * @var \WooCommerce\SheerID\Admin\Tables\VerificationsTable
	 */
	private $table;

	public function __construct( BaseClient $client ) {
		parent::__construct();
		$this->client = $client;
		$this->title  = __( 'Verifications', 'sheerid-for-woocommerce' );
	}

	protected function initialize() {
		add_action( 'load-woocommerce_page_wc-sheerid_verifications', [ $this, 'setup_table' ] );
		add_filter( 'wc_sheerid_admin_request_delete_verification', [ $this, 'get_table_view' ], 10, 2 );
	}

	public function setup_table() {
		$this->table = new VerificationsTable( $this->client );
		$this->table->setup();
		$this->table->process_bulk_actions();
		$this->table->prepare_items();
	}

	public function admin_options() {
		wp_enqueue_script( 'wc-enhanced-select' );
		$this->table->display();
	}

	public function get_table_view( $result, $status ) {
		if ( $status === 200 ) {
			$this->setup_table();
			\ob_start();
			$this->table->render_table();
			$result['html'] = ob_get_clean();
		}

		return $result;
	}

}