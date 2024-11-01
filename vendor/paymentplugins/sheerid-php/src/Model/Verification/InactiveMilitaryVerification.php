<?php

namespace SheerID\Model\Verification;

/**
 * @property string $dischargeDate
 * @property string $country
 * @property string $socialSecurityNumber
 */
class InactiveMilitaryVerification extends BaseVerification {

	/**
	 * @return string
	 */
	public function getDischargeDate() {
		return $this->dischargeDate;
	}

	/**
	 * @param string $dischargeDate
	 */
	public function setDischargeDate( $dischargeDate ) {
		$this->dischargeDate = $dischargeDate;

		return $this;
	}

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

	/**
	 * @return string
	 */
	public function getSocialSecurityNumber() {
		return $this->socialSecurityNumber;
	}

	/**
	 * @param string $socialSecurityNumber
	 */
	public function setSocialSecurityNumber( $socialSecurityNumber ) {
		$this->socialSecurityNumber = $socialSecurityNumber;

		return $this;
	}

}