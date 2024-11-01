<?php

namespace WooCommerce\SheerID\Admin\Settings;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Admin\Tables\ProgramsTable;

class ProgramSettings extends AbstractSettings {

	public $id = 'sheerid_program';

	/**
	 * @var ProgramsTable
	 */
	private $table;

	private $client;

	public function __construct( BaseClient $client ) {
		$this->client = $client;
		$this->title  = __( 'Program Settings', 'sheerid-for-woocommerce' );
		parent::__construct();
	}

	protected function initialize() {
		add_action( 'load-woocommerce_page_wc-sheerid_program', [ $this, 'setup_table' ] );
		add_filter( 'wc_sheerid_admin_request_create_program', [ $this, 'get_table_view' ], 10, 2 );
		add_filter( 'wc_sheerid_admin_request_delete_program', [ $this, 'get_table_view' ], 10, 2 );
		add_filter( 'wc_sheerid_admin_request_update_program', [ $this, 'get_table_view' ], 10, 2 );
		add_filter( 'wc_sheerid_admin_request_toggle_program_mode', [ $this, 'get_table_view' ], 10, 2 );
		add_filter( 'wc_sheerid_admin_request_sync_programs', [ $this, 'get_table_view' ], 10, 2 );
	}

	public function setup_table() {
		$this->table = new ProgramsTable( $this->client );
		$this->table->setup();
		$this->table->process_bulk_actions();
		$this->table->prepare_items();
	}

	public function admin_options() {
		?>
        <div class="wrap">
            <div class="sheerid-header">
                <h1 class='wp-heading-inline'><?php esc_html_e( 'Programs', 'sheerid-for-woocommerce' ); ?></h1>
                <div>
                    <button class="button secondary-button" id="sheerid-add-program" data-processing-text="<?php esc_attr_e( 'Processing...', 'sheerid-for-woocommerce' ) ?>">
						<?php esc_html_e( 'Add Program', 'sheerid-for-woocommerce' ) ?>
                    </button>
                    <button class="button secondary-button sync-programs" id="sheerid-sync-programs" data-processing-text="<?php esc_attr_e( 'Syncing...', 'sheerid-for-woocommerce' ) ?>">
						<?php esc_html_e( 'Sync Programs', 'sheerid-for-woocommerce' ) ?>
                    </button>
                </div>
                <div id="sheerid-program-app"></div>
                <div id="program-delete-app"></div>
                <div id="sheerid-edit-program-app"></div>
            </div>
			<?php
			$this->table->display();
			?>
        </div>
		<?php
	}

	public function get_table_view( $result, $status ) {
		if ( $status === 200 ) {
			$this->setup_table();
			\ob_start();
			$this->table->display();
			$result['html'] = ob_get_clean();
		}

		return $result;
	}

	public function supports_save_settings() {
		return false;
	}

}