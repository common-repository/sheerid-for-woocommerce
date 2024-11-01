<?php

namespace WooCommerce\SheerID\Admin\Tables;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Database\VerificationDataStore;
use WooCommerce\SheerID\Verification;

class VerificationsTable extends \WP_List_Table {

	private $client;

	private $programs;

	private $query_args;

	private $request = [];

	public function __construct( BaseClient $client, $args = [] ) {
		parent::__construct( $args );
		$this->client = $client;
	}

	public function setup() {
	}

	public function get_columns() {
		return [
			'cb'              => '<input type="checkbox"/>',
			'verification_id' => esc_html__( 'Verification ID', 'sheerid-for-woocommerce' ),
			'program_id'      => esc_html__( 'Program', 'sheerid-for-woocommerce' ),
			'created_at'      => esc_html__( 'Created', 'sheerid-for-woocommerce' ),
			'user_info'       => esc_html__( 'User Info', 'sheerid-for-woocommerce' ),
			'status'          => esc_html__( 'Status', 'sheerid-for-woocommerce' ),
			//'program_edit'    => esc_html__( 'Actions', 'sheerid-for-woocommerce' )
		];
	}

	public function display() {
		?>
        <div class="wrap">
            <div id="sheerid-edit-verification-app"></div>
            <div id="sheerid-verification-reminder-app"></div>
            <div id="verification-delete-app"></div>
			<?php
			$this->views();
			$this->render_hidden_fields();
			$this->render_search_box( esc_html__( 'Search verifications', 'sheerid-for-woocommerce' ) );
			$this->render_table();
			?>
        </div>
		<?php
	}

	public function render_table() {
		?>
        <div class="verifications-table">
			<?php
			parent::display();
			?>
        </div>
		<?php
	}

	public function prepare_items() {
		// prepare the query
		$this->query_args = [
			'limit'    => $this->get_items_per_page( 'edit_sheerid_verifications_per_page', 10 ),
			'page'     => $this->get_pagenum(),
			'paginate' => true
		];

		$this->add_search_query_args();
		$this->add_status_query_args();
		$this->add_sortable_column_args();
		$this->set_date_query_args();
		$this->set_customer_query_arg();

		$results     = sheerid_wc_get_verifications( $this->query_args );
		$this->items = $results->results;

		if ( ! $this->programs ) {
			$programs = $this->client->programs->all();
			if ( ! is_wp_error( $programs ) ) {
				$this->programs = $programs->items();
			} else {
				$this->programs = [];
			}
		}

		$this->set_pagination_args( [
			'total_items' => $results->count,
			'per_page'    => $this->query_args['limit'],
			'total_pages' => $results->max_num_pages
		] );
	}

	public function get_sortable_columns() {
		return [
			'verification_id' => 'verification_id',
			'status'          => 'status',
			'user_info'       => 'user_id',
			'created_at'      => 'created_at'
		];
	}

	public function get_bulk_actions() {
		return [
			'delete'       => __( 'Delete', 'sheerid-for-woocommerce' ),
			'mark_success' => __( 'Change status to success', 'sheerid-for-woocommerce' ),
			'mark_error'   => __( 'Change status to error', 'sheerid-for-woocommerce' ),
			'sync'         => __( 'Sync local state', 'sheerid-for-woocommerce' )
		];
	}

	public function get_views() {
		$statuses       = sheerid_wc_get_verification_statuses();
		$views          = [];
		$status_counts  = [];
		$all_count      = 0;
		$current_status = isset( $_REQUEST['status'] ) ? sanitize_text_field( \wp_unslash( $_REQUEST['status'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data_store     = \WC_Data_Store::load( VerificationDataStore::ID );
		$counts         = $data_store->count_by_status();
		foreach ( $statuses as $key => $status ) {
			$filtered              = array_filter( $counts, function ( $count ) use ( $key ) {
				return $count->status === $key;
			} );
			$result                = array_shift( $filtered );
			$status_counts[ $key ] = $result->count ?? 0;
			$all_count             += $status_counts[ $key ];
		}

		$views['all'] = $this->get_status_view_link( 'all', __( 'All', 'sheerid-for-woocommerce' ), $all_count, empty( $current_status ) || $current_status === 'all' );

		foreach ( $status_counts as $key => $count ) {
			$views[ $key ] = $this->get_status_view_link( $key, $statuses[ $key ], $count, $current_status === $key );
		}

		return $views;
	}

	private function get_status_view_link( $key, $label, $count, $current = false ) {
		$url   = add_query_arg( [ 'status' => $key ], admin_url( 'admin.php?page=wc-sheerid_verifications' ) );
		$class = 'status-link';
		if ( $current ) {
			$class .= ' current';
		}

		return '<a class="' . $class . '" href="' . $url . '">' . $label . ' (' . $count . ')</a>';
	}

	private function find_program( $program_id ) {
		$results = array_values( array_filter( $this->programs, function ( $program ) use ( $program_id ) {
			return $program->getId() === $program_id;
		} ) );

		return $results[0] ?? null;
	}

	public function column_default( $item, $column_name ) {
		if ( $item ) {
			$method = 'render_' . $column_name . '_column';
			if ( method_exists( $this, $method ) ) {
				$this->{$method}( $item );
			}
		}
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" class="input-verification-id" name="%1$s[]" value="%2$s" />', 'verification_ids', esc_attr( $item->get_id() ) );
	}

	public function render_verification_id_column( Verification $verification ) {
		$actions = [
			'send_reminder' => esc_html__( 'Send reminder', 'sheerid-for-woocommerce' ),
			'delete'        => esc_html__( 'Delete', 'sheerid-for-woocommerce' ),
		];
		// If a verification is successful then a reminder should not be available.
		if ( $verification->is_success() ) {
			unset( $actions['send_reminder'] );
		}
		$links = [];
		foreach ( $actions as $action => $label ) {
			$links[] = '<span class="' . $action . '"><a href="#" data-verification="' . wc_esc_json( wp_json_encode( $verification ) ) . '">' . $label . '</a></span>';
		}
		?>
        <strong><a target="_blank" href="<?php echo esc_attr( 'https://my.sheerid.com/search/customer/?verificationId=' ) . esc_attr( $verification->get_verification_id() ) ?>"><?php echo esc_html( $verification->get_verification_id() ) ?></a></strong>
        <div class="row-actions">
			<?php echo wp_kses_post( implode( ' | ', $links ) ) ?>
        </div>
		<?php
	}

	public function render_program_id_column( Verification $verification ) {
		$program = $this->find_program( $verification->get_program_id() );
		if ( $program ) {
			echo esc_html( $program->getName() );
		}
	}

	public function render_created_at_column( Verification $verification ) {
		$date = $verification->get_created_at();
		echo esc_html( $date->format( 'M j, Y' ) );
	}

	public function render_user_info_column( Verification $verification ) {
		?>
        <div class="user-info">
			<?php
			$email = $verification->get_email();
			if ( \is_numeric( $verification->get_user_id() ) ) {
				$email = '<a href="' . admin_url( 'user-edit.php?user_id=' . $verification->get_user_id() ) . '">' . $verification->get_email() . '</a>';
			}
			?>
            <div class="row">
                <div class="column">
                    <label><?php esc_html_e( 'Email', 'sheerid-for-woocommerce' ); ?>:&nbsp&nbsp;</label>
                    <span><?php echo wp_kses( $email, [ 'a' => [ 'href' => [] ] ] ) ?></span>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <label><?php esc_html_e( 'Name', 'sheerid-for-woocommerce' ); ?>:&nbsp&nbsp;</label>
                    <span><?php printf( '%1$s %2$s', esc_html( $verification->get_first_name() ), esc_html( $verification->get_last_name() ) ) ?></span>
                </div>
            </div>
        </div>
		<?php
	}

	public function render_status_column( Verification $verification ) {
		$statuses = sheerid_wc_get_verification_statuses();
		?>
        <mark class="verification-status status-<?php echo esc_attr( sanitize_html_class( $verification->get_status() ) ) ?>">
            <span><?php echo esc_html( $statuses[ $verification->get_status() ] ) ?></span>
        </mark>
		<?php
	}

	public function process_bulk_actions() {
		$ids = ! empty( $_REQUEST['verification_ids'] ) ? \wc_clean( \wp_unslash( $_REQUEST['verification_ids'] ) ) : []; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $ids ) || ! $this->current_action() ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permissions to execute bulk actions on verifications.', 'sheerid-for-woocommerce' ) );
		}

		switch ( $this->current_action() ) {
			case 'delete':
				foreach ( $ids as $id ) {
					$verification = sheerid_wc_get_verification( $id );
					if ( $verification ) {
						$verification->delete();
					}
				}
			case 'mark_success':
			case 'mark_error':
				$status = substr( $this->current_action(), 5 );
				foreach ( $ids as $id ) {
					$verification = sheerid_wc_get_verification( $id );
					if ( $verification ) {
						$verification->update_status( $status );
					}
				}
				break;
			case 'sync':
				$verifications = sheerid_wc_get_verifications( [ 'ids' => $ids ] );
				foreach ( $verifications as $verification ) {
					$result = $this->client->verificationDetails->retrieve( $verification->get_verification_id() );
					if ( ! is_wp_error( $result ) ) {
						$verification->sync_from_sheerid_verification( $result );
					}
				}
				break;
		}
	}

	private function add_status_query_args() {
		if ( isset( $_REQUEST['status'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->query_args['status'] = sanitize_text_field( \wp_unslash( $_REQUEST['status'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $this->query_args['status'] === 'all' ) {
				unset( $this->query_args['status'] );
			}
		}
	}

	private function add_search_query_args() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['search'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->query_args['search'] = sanitize_text_field( \wp_unslash( $_REQUEST['search'] ) );
		}
	}

	private function add_sortable_column_args() {
		$this->query_args['order_by'] = 'created_at';
		$this->query_args['order']    = 'DESC';

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['orderby'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->query_args['order_by'] = sanitize_text_field( \wp_unslash( $_REQUEST['orderby'] ) );
		}
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['order'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->query_args['order'] = sanitize_text_field( \wp_unslash( $_REQUEST['order'] ) );
		}
	}

	private function set_date_query_args() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['date_filter'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$start_date = \DateTime::createFromFormat( 'Y-m-d', \sanitize_text_field( \wp_unslash( $_REQUEST['date_filter'] ) ) );
			$end_date   = clone $start_date;
			$end_date->modify( 'last day of this month' );
			$this->query_args['date_range'] = [ $start_date->format( 'Y-m-d' ), $end_date->format( 'Y-m-d' ) ];
		}
	}

	private function set_customer_query_arg() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['_customer_user'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$user_id = (int) sanitize_text_field( \wp_unslash( $_REQUEST['_customer_user'] ) );
			if ( $user_id > 0 ) {
				$this->query_args['user_id'] = $user_id;
			}
		}
	}

	private function render_search_box( $label ) {
		$value = '';
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['search'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$value = sanitize_text_field( \wp_unslash( $_REQUEST['search'] ) );
		}
		?>
        <p class="search-box">
            <label class="screen-reader-text" for=""><?php echo esc_html( $label ) ?></label>
            <input type="search" name="search" value="<?php echo esc_html( $value ) ?>"/>
			<?php submit_button( $label, '', '', false ) ?>
        </p>
		<?php
	}

	private function render_hidden_fields() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['status'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<input type="hidden" name="status" value="' . esc_html( sanitize_text_field( \wp_unslash( $_REQUEST['status'] ) ) ) . '"/>';
		}
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['paged'] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<input type="hidden" name="paged" value="' . esc_html( sanitize_text_field( \wp_unslash( $_REQUEST['paged'] ) ) ) . '"/>';
		}
	}

	public function extra_tablenav( $which ) {
		$date = new \DateTime();
		$date->setDate( $date->format( 'Y' ), $date->format( 'm' ), 1 );
		$user_string = '';
		$user_id     = '';
		$start_date  = '';
		if ( ! empty( $this->query_args['user_id'] ) ) {
			$user        = new \WP_User( $this->query_args['user_id'] );
			$user_id     = $user->ID;
			$user_string = sprintf(
			/* translators: 1: user display name 2: user ID 3: user email */
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'sheerid-for-woocommerce' ),
				$user->display_name,
				absint( $user->ID ),
				$user->user_email
			);
		}
		if ( ! empty( $this->query_args['date_range'] ) ) {
			$start_date = $this->query_args['date_range'][0];
		}
		if ( $which === 'top' ) {
			?>
            <div class="alignleft actions">
                <select id="filter-by-date" name="date_filter">
                    <option value="0"><?php esc_html_e( 'All dates', 'sheerid-for-woocommerce' ); ?></option>
					<?php for ( $i = 0; $i < 12; $i ++ ): ?>
                        <option value="<?php echo esc_html( $date->format( 'Y-m-d' ) ) ?>" <?php selected( $start_date, $date->format( 'Y-m-d' ) ) ?>><?php echo esc_html( $date->format( 'M, Y' ) ) ?></option>
						<?php $date->modify( "first day of -1 month" ); ?>
					<?php endfor; ?>
                </select>
                <select class="wc-customer-search" name="_customer_user" data-placeholder="<?php esc_attr_e( 'Filter by registered customer', 'sheerid-for-woocommerce' ); ?>" data-allow_clear="true">
                    <option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo wp_kses_post( htmlspecialchars( $user_string ) ); ?></option>
                </select>
				<?php
				submit_button( __( 'Filter', 'sheerid-for-woocommerce' ), '', 'filter_results', false, [ 'id' => 'submit-filter-results' ] );
				?>
            </div>
			<?php
		}
	}

	protected function pagination( $which ) {
		$mappings = [ 'order_by' => 'orderby' ];
		$args     = $this->query_args;
		unset( $args['page'], $args['paginate'] );
		foreach ( $mappings as $from => $to ) {
			if ( isset( $args[ $from ] ) ) {
				$args[ $to ] = $args[ $from ];
				unset( $args[ $from ] );
			}
		}
		//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$request_uri            = add_query_arg( $args, \sanitize_text_field( \wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		$_SERVER['REQUEST_URI'] = $request_uri;
		parent::pagination( $which );
	}

}