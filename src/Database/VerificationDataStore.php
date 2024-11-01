<?php

namespace WooCommerce\SheerID\Database;

class VerificationDataStore implements DataStoreInterface {

	const ID = 'sheerid-verification';

	private $count_sql;

	public function create( $verification ) {
		global $wpdb;
		$wpdb->insert( $this->get_table(), [
			'user_id'         => $verification->get_user_id(),
			'verification_id' => $verification->get_verification_id(),
			'program_id'      => $verification->get_program_id(),
			'expiration'      => ! $verification->get_expiration() ? time() + YEAR_IN_SECONDS : $verification->get_expiration(),
			'created_at'      => current_time( 'mysql' ),
			'status'          => $verification->get_status(),
			'mode'            => $verification->get_mode(),
			'first_name'      => $verification->get_first_name(),
			'last_name'       => $verification->get_last_name(),
			'email'           => $verification->get_email(),
			'segment'         => $verification->get_segment()
		], [
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		] );
		$verification->set_id( $wpdb->insert_id );
	}

	public function read( $verification ) {
		global $wpdb;
		$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->sheerid_verifications} WHERE id = %s", $verification->get_id() ) );
		if ( $data ) {
			$verification->set_props( [
				'user_id'         => $data->user_id,
				'verification_id' => $data->verification_id,
				'program_id'      => $data->program_id,
				'expiration'      => $data->expiration,
				'created_at'      => $data->created_at,
				'updated_at'      => $data->updated_at,
				'first_name'      => $data->first_name,
				'last_name'       => $data->last_name,
				'email'           => $data->email
			] );
			$verification->set_id( $data->id );
			$verification->set_object_read( true );
		}
	}

	public function update( $verification ) {
		global $wpdb;
		$changes = $verification->get_changes();
		if ( $changes ) {
			$changes = array_merge( $changes, [
				'updated_at' => current_time( 'mysql' )
			] );
			$wpdb->update(
				$this->get_table(),
				$changes,
				[ 'id' => $verification->get_id() ],
				'%s'
			);
		}
	}

	public function delete( $verification ) {
		global $wpdb;
		$wpdb->delete( $this->get_table(), [ 'id' => $verification->get_id() ], [ '%d' ] );
	}

	public function query( $args ) {
		global $wpdb;
		$args = wp_parse_args( $args, [
			'id'              => '',
			'ids'             => [],
			'program'         => '',
			'program_id'      => '',
			'user_id'         => '',
			'verification'    => '',
			'verification_id' => '',
			'mode'            => '',
			'status'          => [],
			'order'           => 'DESC',
			'order_by'        => 'id',
			'paginate'        => false,
			'page'            => 1,
			'limit'           => 20,
			'search'          => '',
			'date_range'      => []
		] );

		if ( ! is_array( $args['status'] ) ) {
			$args['status'] = [ $args['status'] ];
		}

		$query   = "SELECT * FROM {$wpdb->sheerid_verifications}";
		$queries = (object) [
			'where'    => '',
			'order_by' => "ORDER BY {$args['order_by']} {$args['order']}",
			'limit'    => "LIMIT {$args['limit']}",
			'offset'   => 'OFFSET ' . ( $args['page'] - 1 ) * $args['limit']
		];
		$where   = [];

		if ( $args['id'] ) {
			$where[] = $wpdb->prepare( '`id` = %d', absint( $args['id'] ) );
		}
		if ( $args['ids'] ) {
			$where[] = "id IN('" . implode( "','", array_map( 'esc_sql', $args['ids'] ) ) . "')";
		}
		if ( $args['program'] ) {
			$where[] = $wpdb->prepare( '`program_id` = %s', $args['program'] );
		}
		if ( ! $args['program'] && $args['program_id'] ) {
			$where[] = $wpdb->prepare( '`program_id` = %s', $args['program_id'] );
		}
		if ( $args['user_id'] ) {
			$where[] = $wpdb->prepare( '`user_id` = %s', $args['user_id'] );
		}
		if ( $args['verification'] ) {
			$where[] = $wpdb->prepare( '`verification_id` = %s', $args['verification'] );
		}
		if ( ! $args['verification'] && $args['verification_id'] ) {
			$where[] = $wpdb->prepare( '`verification_id` = %s', $args['verification_id'] );
		}
		if ( $args['status'] ) {
			$where[] = "status IN('" . implode( "','", array_map( 'esc_sql', $args['status'] ) ) . "')";
		}
		if ( $args['mode'] ) {
			$where[] = $wpdb->prepare( '`mode` = %s', $args['mode'] );
		}
		if ( $args['search'] ) {
			$where[] = $wpdb->prepare( 'verification_id LIKE %s', '%' . $wpdb->esc_like( $args['search'] ) . '%' );
		}
		if ( ! empty( $args['date_range'] ) ) {
			$where[] = $wpdb->prepare( 'created_at >= %s AND created_at <= %s', $args['date_range'][0], $args['date_range'][1] );
		}
		if ( $where ) {
			$queries->where = ' WHERE ' . implode( ' AND ', $where );
		}

		$query .= " {$queries->where} {$queries->order_by} {$queries->limit} {$queries->offset}";

		$this->count_sql = "SELECT COUNT(DISTINCT(id)) AS count FROM {$this->get_table()} {$queries->where}";

		if ( $args['paginate'] ) {
			$count   = (int) $wpdb->get_var( $this->count_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$results = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			return (object) [
				'results'       => $results,
				'max_num_pages' => \ceil( $count / $args['limit'] ),
				'count'         => $count
			];
		}

		return $wpdb->get_results( $query ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	public function count_by_status( $args = [] ) {
		global $wpdb;

		return $wpdb->get_results( "SELECT status, COUNT(*) AS count FROM {$wpdb->sheerid_verifications} GROUP BY status" );
	}

	public function update_user_id( $new_user_id, $user_id ) {
		global $wpdb;
		if ( $new_user_id !== $user_id ) {
			$wpdb->update( $this->get_table(), [ 'user_id' => $new_user_id ], [ 'user_id' => $user_id ], '%s' );
		}
	}

	public function get_table() {
		global $wpdb;

		return $wpdb->sheerid_verifications;
	}

}