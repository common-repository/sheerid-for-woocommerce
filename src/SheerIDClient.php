<?php

namespace WooCommerce\SheerID;

use SheerID\Client\BaseClient;
use SheerID\Client\HttpInterface;
use SheerID\Exception\BaseException;

class SheerIDClient extends BaseClient {

	public function __construct( $config, HttpInterface $http = null ) {
		parent::__construct( $config, $http );
	}

	/**
	 * @var \WooCommerce\SheerID\Logger
	 */
	private $log;

	public function request( $method, $path, $clazz = null, $params = null, $opts = null ) {
		try {
			return parent::request( $method, $path, $clazz, $params, $opts );
		} catch ( BaseException $e ) {
			// log the failed API request
			$this->log->error( sprintf( 'API request error: %1$s', print_r( [
				'method'      => $method,
				'path'        => $path,
				'http_status' => $e->getCode()
			], true ) ) );

			return new \WP_Error(
				'sheerid_error',
				$e->getMessage(),
				[
					'status' => $e->getHttpStatus(),
					'type'   => get_class( $e )
				]
			);
		}
	}

	public function set_log( $log ) {
		$this->log = $log;
	}

}