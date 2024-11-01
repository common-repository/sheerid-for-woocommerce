<?php

namespace SheerID\Model;

/**
 * @property string $verificationId
 * @property string $currentStep
 * @property string $rewardCode
 * @property string[] $errorIds
 * @property string $segment
 * @property string $subSegment
 * @property string $locale
 * @property string[] $rejectionReasons
 */
class LastResponse extends AbstractModel {

	const MODEL_TYPE = 'lastResponse';

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
	 * @return string
	 */
	public function getRewardCode() {
		return $this->rewardCode;
	}

	/**
	 * @param string $rewardCode
	 */
	public function setRewardCode( $rewardCode ) {
		$this->rewardCode = $rewardCode;

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
	 * @return string[]
	 */
	public function getRejectionReasons() {
		return $this->rejectionReasons;
	}

	/**
	 * @param string[] $rejectionReasons
	 */
	public function setRejectionReasons( $rejectionReasons ) {
		$this->rejectionReasons = $rejectionReasons;

		return $this;
	}
}