<?php

namespace SheerID\Model;

/**
 * @property string $name
 * @property string $displayName
 * @property string $description
 * @property string $label
 * @property DisplayInfo $displayInfo
 * @property array $languages
 * @property array $countries
 */
class SegmentDescription extends AbstractModel {

	const MODEL_TYPE = 'segmentDescription';

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDisplayName() {
		return $this->displayName;
	}

	/**
	 * @param string $displayName
	 */
	public function setDisplayName( $displayName ) {
		$this->displayName = $displayName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel( $label ) {
		$this->label = $label;

		return $this;
	}

	/**
	 * @return DisplayInfo
	 */
	public function getDisplayInfo() {
		return $this->displayInfo;
	}

	/**
	 * @param DisplayInfo $displayInfo
	 */
	public function setDisplayInfo( $displayInfo ) {
		$this->displayInfo = $displayInfo;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getLanguages() {
		return $this->languages;
	}

	/**
	 * @param array $languages
	 */
	public function setLanguages( $languages ) {
		$this->languages = $languages;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getCountries() {
		return $this->countries;
	}

	/**
	 * @param array $countries
	 */
	public function setCountries( $countries ) {
		$this->countries = $countries;

		return $this;
	}


}