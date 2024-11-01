<?php

namespace WooCommerce\SheerID\Admin\Tables;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class ProgramsTable extends \WP_List_Table {

	/**
	 * @var \SheerID\Client\BaseClient
	 */
	private $client;

	public function __construct( $client, $args = array() ) {
		parent::__construct( $args );
		$this->client = $client;
	}

	public function setup() {
		add_filter( "manage_{$this->screen->id}_columns", [ $this, 'get_columns' ], 0 );
	}

	public function get_columns() {
		return [
			'cb'           => '<input type="checkbox"/>',
			'program_id'   => esc_html__( 'Program', 'sheerid-for-woocommerce' ),
			'program_name' => esc_html__( 'Name', 'sheerid-for-woocommerce' ),
			'program_type' => esc_html__( 'Type', 'sheerid-for-woocommerce' ),
			'program_mode' => esc_html__( 'Mode', 'sheerid-for-woocommerce' ),
			'program_edit' => esc_html__( 'Actions', 'sheerid-for-woocommerce' )
		];
	}

	public function prepare_items() {
		// fetch the existing programs
		$programs = $this->client->programs->all();
		if ( ! is_wp_error( $programs ) ) {
			$this->items = $programs->items();
		}
	}

	public function column_default( $item, $column_name ) {
		if ( $item ) {
			$method = 'render_' . $column_name . '_column';
			if ( method_exists( $this, $method ) ) {
				$this->{$method}( $item );
			}
		}
	}

	/**
	 * @param \SheerID\Model\Program $item
	 *
	 * @return void
	 */
	public function render_program_id_column( $item ) {
		$links[] = '<span class="edit"><a class="edit" href="#" data-program="' . esc_attr( $item->getId() ) . '" data-text="' . esc_attr__( 'Processing...', 'sheerid-for-woocommerce' ) . '">' . esc_html__( 'Edit', 'sheerid-for-woocommerce' ) . '</a></span>';
		$links[] = '<span class="delete"><a class="delete" href="#" data-program="' . esc_attr( $item->getId() ) . '">' . esc_html__( 'Delete', 'sheerid-for-woocommerce' ) . '</a></span>';
		if ( $item->isLive() ) {
			$links[] = '<span class="unlaunch"><a class="toggle-program-mode" href="#" data-program="' . esc_attr( $item->getId() ) . '" data-text="' . esc_attr__( 'Processing...', 'sheerid-for-woocommerce' ) . '">' . esc_html__( 'Un-launch', 'sheerid-for-woocommerce' ) . '</a></span>';
		} else {
			$links[] = '<span class="launch"><a class="toggle-program-mode" href="#" data-program="' . esc_attr( $item->getId() ) . '" data-text="' . esc_attr__( 'Processing...', 'sheerid-for-woocommerce' ) . '">' . esc_html__( 'Launch', 'sheerid-for-woocommerce' ) . '</a></span>';
		}
		?>
        <div class="sheerid-program-id <?php echo esc_html( $item->getSegmentDescription()->getName() ) ?>">
            <div class="program-icon">
                <svg viewBox="-10 -10 100 100">
                    <image href="<?php echo esc_html( esc_url( $item->getSegmentDescription()->getDisplayInfo()->getIconSrc() ) ) ?>"/>
                </svg>
            </div>
            <strong><?php echo esc_html( $item->getId() ) ?></strong>
        </div>
        <div class="row-actions">
			<?php echo wp_kses_post( implode( ' | ', $links ) ) ?>
        </div>
		<?php
	}

	public function render_program_name_column( $item ) {
		echo esc_html( $item->getName() );
	}

	public function render_program_mode_column( $item ) {
		$text = __( 'Live', 'sheerid-for-woocommerce' );
		if ( ! $item->isLive() ) {
			$text = __( 'Test', 'sheerid-for-woocommerce' );
		}
		?>
        <mark class="sheerid-program-mode <?php echo $item->isLive() ? 'live-mode' : 'test-mode' ?>">
            <span><?php echo esc_html( $text ) ?></span>
        </mark>
		<?php
	}

	/**
	 * @param \SheerID\Model\Program $item
	 *
	 * @return void
	 */
	public function render_program_type_column( $item ) {
		echo esc_html( $item->getSegmentDescription()->getDisplayName() );
	}

	/**
	 * @param \SheerID\Model\Program $item
	 *
	 * @return void
	 */
	public function render_program_edit_column( $item ) {
		?>
        <div class="menu-items-container">
            <div class="program-edit-ellipses">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="menu-item-container">
                <div>
                    <a class="edit-program"
                       data-program="<?php echo esc_attr( $item->getId() ) ?>"
                       data-text="<?php esc_attr_e( 'Processing...', 'sheerid-for-woocommerce' ) ?>">
						<?php esc_html_e( 'Edit program', 'sheerid-for-woocommerce' ) ?>
                    </a>
                </div>
                <div>
                    <a class="delete-program" data-program="<?php echo esc_attr( $item->getId() ) ?>">
						<?php esc_html_e( 'Delete program', 'sheerid-for-woocommerce' ) ?>
                    </a>
                </div>
                <div>
                    <a class="toggle-program-mode"
                       data-program="<?php echo esc_attr( $item->getId() ) ?>"
                       data-text="<?php esc_attr_e( 'Processing...', 'sheerid-for-woocommerce' ) ?>">
						<?php $item->isLive() ? esc_html_e( 'Un-launch program', 'sheerid-for-woocommerce' ) : esc_html_e( 'Launch program', 'sheerid-for-woocommerce' ) ?>
                    </a>
                </div>
            </div>
        </div>
		<?php
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', 'program_ids', esc_attr( $item->getId() ) );
	}

	public function get_bulk_actions() {
		return [
			'delete' => __( 'Delete', 'sheerid-for-woocommerce' )
		];
	}

	public function display() {
		?>
        <div class="programs-table">
			<?php parent::display(); ?>
        </div>
		<?php
	}

	public function process_bulk_actions() {
		$action = $this->current_action();
		$ids    = isset( $_REQUEST['program_ids'] ) ? \wc_clean( \wp_unslash( $_REQUEST['program_ids'] ) ) : []; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permissions to execute bulk actions on programs.', 'sheerid-for-woocommerce' ) );
		}

		if ( $action === 'delete' ) {
			foreach ( $ids as $id ) {
				$this->client->programs->archive( $id );
			}
		}
	}

}