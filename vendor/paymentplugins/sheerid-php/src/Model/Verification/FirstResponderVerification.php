<?php

namespace SheerID\Model\Verification;

/**
 * @property array $statuses
 * @property string $stateCode
 * @property string $country
 */
class FirstResponderVerification extends BaseVerification {

	const POLICE = 'POLICE';

	const EMT = 'EMT';

	const FIREFIGHTER = 'FIREFIGHTER';

	const SEARCH_AND_RESCUE = 'SEARCH_AND_RESCUE';

	/**
	 * @return array
	 */
	public function getStatuses() {
		return $this->statuses;
	}

	/**
	 * @param array $statuses
	 */
	public function setStatuses( $statuses ) {
		$this->statuses = $statuses;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStateCode() {
		return $this->stateCode;
	}

	/**
	 * @param string $stateCode
	 */
	public function setStateCode( $stateCode ) {
		$this->stateCode = $stateCode;

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


}