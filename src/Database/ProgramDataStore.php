<?php

namespace WooCommerce\SheerID\Database;

class ProgramDataStore implements DataStoreInterface {

	const ID = 'sheerid-program';

	protected function get_table() {
		global $wpdb;

		return $wpdb->sheerid_programs;
	}

	/**
	 * @param \WooCommerce\SheerID\Program $data
	 *
	 * @return void
	 */
	public function create( $data ) {
		global $wpdb;
		$wpdb->insert( $this->get_table(), [
			'program_id'     => $data->get_program_id(),
			'mode'           => $data->get_mode(),
			'webhooks'       => maybe_serialize( $data->get_webhooks() ),
			'success_email'  => $data->get_success_email(),
			'failure_email'  => $data->get_failure_email(),
			'reminder_email' => $data->get_reminder_email(),
			'created_at'     => current_time( 'mysql' )
		] );
		$data->set_id( $wpdb->insert_id );
		$data->set_object_read( true );
	}

	/**
	 * @param \WooCommerce\SheerID\Program $data
	 *
	 * @return void
	 */
	public function read( $data ) {
		global $wpdb;
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT FROM {$wpdb->sheerid_programs} WHERE id = %d", $data->get_id() ) );
		if ( $result ) {
			$data->set_props( [
				'program_id'     => $result->program_id,
				'webhooks'       => maybe_unserialize( $result->webhooks ),
				'success_email'  => $result->success_email,
				'failure_email'  => $result->failure_email,
				'reminder_email' => $result->reminder_email,
				'created_at'     => $data->created_at,
				'updated_at'     => $data->updated_at
			] );
			$data->set_id( $result->id );
			$data->set_object_read( true );
		}
	}

	public function update( $data ) {
		global $wpdb;
		$changes = $data->get_changes();
		if ( $changes ) {
			if ( isset( $changes['webhooks'] ) ) {
				$changes['webhooks'] = maybe_serialize( $changes['webhooks'] );
			}
			$wpdb->update(
				$this->get_table(),
				$changes,
				[ 'id' => $data->get_id() ],
				'%s'
			);
		}
	}

	public function delete( $data ) {
		global $wpdb;
		$wpdb->delete( $this->get_table(), [ 'id' => $data->get_id() ], [ '%d' ] );
	}

	public function query( $args ) {
		global $wpdb;
		$args = wp_parse_args( $args, [
			'program_id'  => '',
			'order'       => 'DESC',
			'order_by'    => 'id',
			'ids'         => [],
			'program_ids' => []
		] );

		$query = "SELECT * FROM {$this->get_table()}";

		$where = [];

		if ( $args['program_id'] ) {
			$where[] = $wpdb->prepare( "`program_id` = %s", $args['program_id'] );
		}
		if ( $args['program_ids'] ) {
			$where[] = "program_id IN('" . implode( "','", array_map( 'esc_sql', $args['program_ids'] ) ) . "')";
		}
		if ( $args['ids'] ) {
			$where[] = "id IN('" . implode( "','", array_map( 'esc_sql', $args['ids'] ) ) . "')";
		}

		if ( $where ) {
			$query .= " WHERE " . implode( ' AND ', $where );
		}

		$query .= " ORDER BY {$args['order_by']} {$args['order']}";

		return $wpdb->get_results( $query ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

}