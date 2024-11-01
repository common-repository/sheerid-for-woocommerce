<?php

namespace SheerID\Model\Verification;

/**
 * @property boolean $marketConsent
 * @property string $city
 * @property string $address1
 */
class AgeVerification extends BaseVerification {
	/**
	 * @return bool
	 */
	public function isMarketConsent() {
		return $this->marketConsent;
	}

	/**
	 * @param bool $marketConsent
	 */
	public function setMarketConsent( $marketConsent ) {
		$this->marketConsent = $marketConsent;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @param string $city
	 */
	public function setCity( $city ) {
		$this->city = $city;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAddress1() {
		return $this->address1;
	}

	/**
	 * @param string $address1
	 */
	public function setAddress1( $address1 ) {
		$this->address1 = $address1;

		return $this;
	}

}