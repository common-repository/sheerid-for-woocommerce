<?php

namespace SheerID\Model;

/**
 * @property string $userId
 * @property string $bearerToken
 */
class LoginResponse extends AbstractModel {
	/**
	 * @return string
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @param string $userId
	 */
	public function setUserId( $userId ) {
		$this->userId = $userId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBearerToken() {
		return $this->bearerToken;
	}

	/**
	 * @param string $bearerToken
	 */
	public function setBearerToken( $bearerToken ) {
		$this->bearerToken = $bearerToken;

		return $this;
	}

}