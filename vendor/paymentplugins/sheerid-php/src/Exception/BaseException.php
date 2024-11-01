<?php

namespace SheerID\Exception;

/**
 * Base exception that all exception classes should extend.
 */
class BaseException extends \Exception {

	private $httpStatus;

	private $json;

	private $errorIds;


	public static function factory( $status, $json ): BaseException {
		$msg      = $json['systemErrorMessage'] ?? '';
		$errorIds = $json['errorIds'] ?? [];

		$exception = new static( $msg );
		$exception->setHttpStatus( $status );
		$exception->setErrorIds( $errorIds );
		$exception->setJson( $json );

		return $exception;
	}

	public function setHttpStatus( $status ) {
		$this->httpStatus = $status;
	}

	public function setJson( $json ) {
		$this->json = $json;
	}

	public function setErrorIds( $ids ) {
		$this->errorIds = $ids;
	}

	public function getHttpStatus() {
		return $this->httpStatus;
	}

	public function getErrorIds() {
		return $this->errorIds;
	}

	public function getJson() {
		return $this->json;
	}

	/**
	 * Returns the first entry in the errorIds array.
	 * @return false|mixed|null
	 */
	public function getErrorId() {
		if ( ! empty( $this->errorIds ) ) {
			return \reset( $this->errorIds );
		}

		return null;
	}
}