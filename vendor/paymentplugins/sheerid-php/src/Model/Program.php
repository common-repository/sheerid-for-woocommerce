<?php

namespace SheerID\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $offerDescription
 * @property SegmentDescription $segmentDescription
 * @property string $logoUrl
 * @property string $customCss
 * @property boolean $live
 * @property boolean $archived
 * @property InstallOptions $installOptions
 * @property CustomerTagging $customerTagging
 * @property string $oemType
 * @property string $oemUniqueId
 * @property CustomCssRequest $customCssRequest
 * @property Audience $audience
 */
class Program extends AbstractModel {

	const MODEL_TYPE = 'program';

	const MODEL_TYPES = 'programs';

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( $id ) {
		$this->id = $id;

		return $this;
	}

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
	public function getOfferDescription() {
		return $this->offerDescription;
	}

	/**
	 * @param string $offerDescription
	 */
	public function setOfferDescription( $offerDescription ) {
		$this->offerDescription = $offerDescription;

		return $this;
	}

	/**
	 * @return SegmentDescription
	 */
	public function getSegmentDescription() {
		return $this->segmentDescription;
	}

	/**
	 * @param SegmentDescription $segmentDescription
	 */
	public function setSegmentDescription( $segmentDescription ) {
		$this->segmentDescription = $segmentDescription;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogoUrl() {
		return $this->logoUrl;
	}

	/**
	 * @param string $logoUrl
	 */
	public function setLogoUrl( $logoUrl ) {
		$this->logoUrl = $logoUrl;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCustomCss() {
		return $this->customCss;
	}

	/**
	 * @param string $customCss
	 */
	public function setCustomCss( $customCss ) {
		$this->customCss = $customCss;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isLive() {
		return $this->live;
	}

	/**
	 * @param bool $live
	 */
	public function setLive( $live ) {
		$this->live = $live;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isArchived() {
		return $this->archived;
	}

	/**
	 * @param bool $archived
	 */
	public function setArchived( $archived ) {
		$this->archived = $archived;

		return $this;
	}

	/**
	 * @return InstallOptions
	 */
	public function getInstallOptions() {
		return $this->installOptions;
	}

	/**
	 * @param InstallOptions $installOptions
	 */
	public function setInstallOptions( $installOptions ) {
		$this->installOptions = $installOptions;

		return $this;
	}

	/**
	 * @return CustomerTagging
	 */
	public function getCustomerTagging() {
		return $this->customerTagging;
	}

	/**
	 * @param CustomerTagging $customerTagging
	 */
	public function setCustomerTagging( $customerTagging ) {
		$this->customerTagging = $customerTagging;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getOemType() {
		return $this->oemType;
	}

	/**
	 * @param string $oemType
	 */
	public function setOemType( $oemType ) {
		$this->oemType = $oemType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getOemUniqueId() {
		return $this->oemUniqueId;
	}

	/**
	 * @param string $oemUniqueId
	 */
	public function setOemUniqueId( $oemUniqueId ) {
		$this->oemUniqueId = $oemUniqueId;

		return $this;
	}

	/**
	 * @return CustomCssRequest
	 */
	public function getCustomCssRequest() {
		return $this->customCssRequest;
	}

	/**
	 * @param CustomCssRequest $customCssRequest
	 */
	public function setCustomCssRequest( $customCssRequest ) {
		$this->customCssRequest = $customCssRequest;

		return $this;
	}

	/**
	 * @return Audience
	 */
	public function getAudience() {
		return $this->audience;
	}

	/**
	 * @param Audience $audience
	 */
	public function setAudience( $audience ) {
		$this->audience = $audience;

		return $this;
	}

}