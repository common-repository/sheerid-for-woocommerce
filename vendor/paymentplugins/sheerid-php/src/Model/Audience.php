<?php

namespace SheerID\Model;

/**
 * @property SupportedLanguage $supportedLanguage
 * @property array $supportedCountries
 * @property SegmentDetails $segmentDetails
 */
class Audience extends AbstractModel {

	const MODEL_TYPE = 'audience';

	/**
	 * @return SupportedLanguage
	 */
	public function getSupportedLanguage() {
		return $this->supportedLanguage;
	}

	/**
	 * @param SupportedLanguage $supportedLanguage
	 */
	public function setSupportedLanguage( $supportedLanguage ) {
		$this->supportedLanguage = $supportedLanguage;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getSupportedCountries() {
		return $this->supportedCountries;
	}

	/**
	 * @param array $supportedCountries
	 */
	public function setSupportedCountries( $supportedCountries ) {
		$this->supportedCountries = $supportedCountries;

		return $this;
	}

	/**
	 * @return SegmentDetails
	 */
	public function getSegmentDetails() {
		return $this->segmentDetails;
	}

	/**
	 * @param SegmentDetails $segmentDetails
	 */
	public function setSegmentDetails( $segmentDetails ) {
		$this->segmentDetails = $segmentDetails;

		return $this;
	}

}