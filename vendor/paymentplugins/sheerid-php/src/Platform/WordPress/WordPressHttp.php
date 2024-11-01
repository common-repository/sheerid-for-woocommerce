<?php

namespace SheerID\Platform\WordPress;

use SheerID\Http\HttpInterface;

/**
 * Http client for the WordPress platform
 */
class WordPressHttp implements HttpInterface {

	private $http;

	public function __construct() {
		$this->http = new \WP_Http();
	}

	public function request( $method, $url, $params, $headers ) {
		$options = \array_merge( $this->getDefaultOptions(), [
			'method'     => strtoupper( $method ),
			'headers'    => $headers,
			'user-agent' => ''
		] );
		if ( $method === 'GET' ) {
			if ( $params ) {
				$url = add_query_arg( $params, $url );
			}
		} else {
			if ( $params ) {
				$options['body'] = \json_encode( $params );
			}
		}

		$response = $this->http->request( $url, $options );

		if ( \is_wp_error( $response ) ) {
			$status = 400;
			$body   = [ 'systemErrorMessage' => $response->get_error_message() ];
		} else {
			$status = \wp_remote_retrieve_response_code( $response );
			$body   = \wp_remote_retrieve_body( $response );
			if ( $body ) {
				$body = \json_decode( $body, true );
			}
		}

		return [ $body, $status ];
	}

	private function getDefaultOptions() {
		return [
			'timeout' => 11
		];
	}

}