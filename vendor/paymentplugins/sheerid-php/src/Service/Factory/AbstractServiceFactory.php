<?php

namespace SheerID\Service\Factory;

use SheerID\Client\ClientInterface;

abstract class AbstractServiceFactory {

	private $client;

	private $services = [];

	public function __construct( ClientInterface $client ) {
		$this->client = $client;
	}

	public function get( $key ) {
		if ( ! isset( $this->services[ $key ] ) ) {
			$serviceClass = $this->getServiceClass( $key );
			if ( ! $serviceClass ) {
				\trigger_error( 'No service class found for key ' . $key );
			}
			$this->services[ $key ] = new $serviceClass( $this->client );
		}

		return $this->services[ $key ];
	}

	abstract protected function getServiceClass( $key );
}