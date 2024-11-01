<?php

function sheerid_wc_container() {
	return \WooCommerce\SheerID\Plugin::container();
}

/**
 * @param array|int $args
 *
 * @return \WooCommerce\SheerID\Verification|null
 * @throws \Exception
 */
function sheerid_wc_get_verification( $args = [] ) {
	if ( is_numeric( $args ) ) {
		$args = [ 'id' => absint( $args ) ];
	}
	$results = WC_Data_Store::load( \WooCommerce\SheerID\Database\VerificationDataStore::ID )->query( $args );
	if ( ! empty( $results ) ) {
		return new \WooCommerce\SheerID\Verification( $results[0] );
	}

	return null;
}

/**
 * @param $args
 *
 * @return \WooCommerce\SheerID\Verification[]
 * @throws \Exception
 */
function sheerid_wc_get_verifications( $args = [] ) {
	$results       = WC_Data_Store::load( \WooCommerce\SheerID\Database\VerificationDataStore::ID )->query( $args );
	$verifications = [];
	if ( gettype( $results ) == 'object' && isset( $results->results ) && \is_array( $results->results ) ) {
		foreach ( $results->results as $idx => $result ) {
			$results->results[ $idx ] = new \WooCommerce\SheerID\Verification( $result );
		}

		return $results;
	} elseif ( \is_array( $results ) ) {
		foreach ( $results as $result ) {
			$verifications[] = new \WooCommerce\SheerID\Verification( $result );
		}
	}

	return $verifications;
}

/**
 * @param $args
 *
 * @return \WooCommerce\SheerID\Program|null
 * @throws \Exception
 */
function sheerid_wc_get_program( $args = [] ) {
	$results = WC_Data_Store::load( \WooCommerce\SheerID\Database\ProgramDataStore::ID )->query( $args );
	if ( ! empty( $results ) ) {
		return new \WooCommerce\SheerID\Program( $results[0] );
	}

	return null;
}

function sheerid_wc_get_programs( $args = [] ) {
	$programs = [];
	$results  = WC_Data_Store::load( \WooCommerce\SheerID\Database\ProgramDataStore::ID )->query( $args );
	if ( ! empty( $results ) ) {
		foreach ( $results as $result ) {
			$programs[] = new \WooCommerce\SheerID\Program( $result );
		}
	}

	return $programs;
}

function sheerid_wc_get_verification_statuses() {
	return [
		'success'     => __( 'Success', 'sheerid-for-woocommerce' ),
		'error'       => __( 'Error', 'sheerid-for-woocommerce' ),
		'pending'     => __( 'Pending', 'sheerid-for-woocommerce' ),
		'collectInfo' => __( 'Collect Info', 'sheerid-for-woocommerce' ),
		'docUpload'   => __( 'Document Upload', 'sheerid-for-woocommerce' )
	];
}

function wc_sheerid_container() {
	return sheerid_wc_container();
}

function wc_sheerid_get_verification( $args = [] ) {
	return sheerid_wc_get_verification( $args );
}

function wc_sheerid_get_verifications( $args = [] ) {
	return sheerid_wc_get_verifications( $args );
}

function wc_sheerid_get_program( $args = [] ) {
	return sheerid_wc_get_program( $args );
}

function wc_sheerid_get_programs( $args = [] ) {
	return sheerid_wc_get_programs( $args );
}

function wc_sheerid_get_verification_statuses() {
	return sheerid_wc_get_verification_statuses();
}