<?php

namespace SheerID\Client;


use SheerID\Exception\BadRequestException;
use SheerID\Exception\ConflictException;
use SheerID\Exception\LimitExceededException;
use SheerID\Exception\NotAuthorizedException;
use SheerID\Exception\NotFoundException;
use SheerID\Exception\PermissionException;
use SheerID\Http\HttpFactory;
use SheerID\Http\HttpInterface;
use SheerID\Utils\Util;

abstract class AbstractClient implements ClientInterface {

	const BASE_PATH = 'https://services.sheerid.com';

	private $config = [];

	private $http;

	public function __construct( $config, HttpInterface $http = null ) {
		if ( ! \is_array( $config ) ) {
			throw new \InvalidArgumentException( '$config argument must be an array.' );
		}

		$config = \array_merge( $this->getDefaultConfig(), $config );

		$this->validateConfig( $config );

		$this->config = $config;
		$this->http   = $http ?? HttpFactory::getDefaultInstance();
	}

	public function request( $method, $path, $clazz = null, $params = null, $opts = null ) {
		// build up the options array
		$opts = $this->mergeOptions( $this->getDefaultOptions(), $opts );

		// build the request path
		$url = $this->buildRequestUrl( $opts->basePath, $path );

		// make the request
		list( $response, $statusCode ) = $this->http->request( $method, $url, $params, $opts->headers );

		$this->validateResponse( $response, $statusCode );

		// build the response object
		return Util::buildResponseInstance( $response, $clazz );
	}

	private function mergeOptions( $options, $opts ) {
		return (object) Util::mergeDeep( $options, $opts ?? [] );
	}

	private function getDefaultOptions() {
		$defaultOpts = [
			'basePath' => $this->getBasePath(),
			'headers'  => $this->buildRequestHeaders()
		];

		return (object) $defaultOpts;
	}

	private function buildRequestUrl( $basePath, $path ) {
		return $basePath . '/' . Util::trimPath( $path );
	}

	public function getBasePath() {
		return isset( $this->config['base_path'] ) ? $this->config['base_path'] : self::BASE_PATH;
	}

	protected function buildRequestHeaders() {
		return [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->config['token']
		];
	}

	protected function getDefaultConfig() {
		return [
			'base_path' => self::BASE_PATH,
			'token'     => null
		];
	}

	protected function validateConfig( $config ) {
		if ( isset( $config['token'] ) && ! \is_string( $config['token'] ) ) {
			throw new \InvalidArgumentException( 'The "token" key must be null or a string.' );
		}
	}

	protected function validateResponse( $response, $status ) {
		if ( 200 > $status || 300 <= $status ) {
			// the status code is in the error range so handle the request error
			$args = [ $status, $response ];
			switch ( $status ) {
				case 400:
					throw BadRequestException::factory( ...$args );
				case 401:
					throw NotAuthorizedException::factory( ...$args );
				case 403:
					throw PermissionException::factory( ...$args );
				case 404:
					throw NotFoundException::factory( ...$args );
				case 409:
					throw ConflictException::factory( ...$args );
				case 429:
					throw LimitExceededException::factory( ...$args );
			}
		}
	}

}