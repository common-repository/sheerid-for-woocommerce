<?php

namespace SheerID\Model\Verification;

/**
 * @property array $statuses
 * @property string $country
 */
class LicensedProfessionalVerification extends BaseVerification {

	const LICENSED_COSMETOLOGIST = 'LICENSED_COSMETOLOGIST';

	const LICENSED_REAL_ESTATE_AGENT = 'LICENSED_REAL_ESTATE_AGENT';

	const VETERINARIAN = 'VETERINARIAN';

	const CHILD_CARE_WORKER = 'CHILD_CARE_WORKER';

	const LIBRARIAN = 'LIBRARIAN';

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