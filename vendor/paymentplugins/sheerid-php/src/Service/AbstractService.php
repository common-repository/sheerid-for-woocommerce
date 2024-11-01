<?php

namespace SheerID\Service;

use SheerID\Client\ClientInterface;

abstract class AbstractService implements ServiceInterface {

	protected $client;

	protected $namespace = '';

	public function __construct( ClientInterface $client ) {
		$this->client = $client;
	}

	protected function getNamespace() {
		return $this->namespace;
	}

	public function request( $method, $path, $clazz = null, $params = null, $opts = null ) {
		return $this->client->request( $method, $path, $clazz, $params, $opts );
	}

	public function get( $path, $clazz = null, $params = null, $opts = null ) {
		return $this->request( 'GET', $path, $clazz, $params, $opts );
	}

	public function post( $path, $clazz = null, $params = null, $opts = null ) {
		return $this->request( 'POST', $path, $clazz, $params, $opts );
	}

	public function put( $path, $clazz = null, $params = null, $opts = null ) {
		return $this->request( 'PUT', $path, $clazz, $params, $opts );
	}

	protected function buildPath( $uri, ...$args ) {
		$path = $uri;
		if ( $this->getNamespace() ) {
			$path = \rtrim( $this->getNamespace(), '/\\' ) . '/' . \ltrim( $path, '/\\' );
		}

		return sprintf( $path, ...$args );;
	}

}