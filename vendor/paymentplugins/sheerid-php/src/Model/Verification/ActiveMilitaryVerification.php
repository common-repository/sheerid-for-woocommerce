<?php

namespace SheerID\Model\Verification;

/**
 * @property string $country
 */
class ActiveMilitaryVerification extends BaseVerification {
	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @param string $country
	 */
	public function setCountry( $country ) {
		$this->country = $country;

		return $this;
	}

}