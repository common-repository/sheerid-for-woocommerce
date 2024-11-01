<?php

namespace SheerID\Model\Verification;

use SheerID\Model\Metadata;
use SheerID\Model\Organization;

/**
 * @property string $verificationId
 * @property string $currentStep
 * @property string[] $errorIds
 * @property string $segment
 * @property string $subSegment
 * @property string $locale
 * @property string $country
 * @property string $submissionUrl
 * @property int $instantMatchAttempts
 */
class BaseVerification extends \SheerID\Model\AbstractModel {
	/**
	 * @return string
	 */
	public function getVerificationId() {
		return $this->verificationId;
	}

	/**
	 * @param string $verificationId
	 */
	public function setVerificationId( $verificationId ) {
		$this->verificationId = $verificationId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrentStep() {
		return $this->currentStep;
	}

	/**
	 * @param string $currentStep
	 */
	public function setCurrentStep( $currentStep ) {
		$this->currentStep = $currentStep;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getErrorIds() {
		return $this->errorIds;
	}

	/**
	 * @param string[] $errorIds
	 */
	public function setErrorIds( $errorIds ) {
		$this->errorIds = $errorIds;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSegment() {
		return $this->segment;
	}

	/**
	 * @param string $segment
	 */
	public function setSegment( $segment ) {
		$this->segment = $segment;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSubSegment() {
		return $this->subSegment;
	}

	/**
	 * @param string $subSegment
	 */
	public function setSubSegment( $subSegment ) {
		$this->subSegment = $subSegment;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @param string $locale
	 */
	public function setLocale( $locale ) {
		$this->locale = $locale;

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
	public function getSubmissionUrl() {
		return $this->submissionUrl;
	}

	/**
	 * @param string $submissionUrl
	 */
	public function setSubmissionUrl( $submissionUrl ) {
		$this->submissionUrl = $submissionUrl;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getInstantMatchAttempts() {
		return $this->instantMatchAttempts;
	}

	/**
	 * @param int $instantMatchAttempts
	 */
	public function setInstantMatchAttempts( $instantMatchAttempts ) {
		$this->instantMatchAttempts = $instantMatchAttempts;

		return $this;
	}

}