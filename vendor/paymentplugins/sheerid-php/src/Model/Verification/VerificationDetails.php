<?php

namespace SheerID\Model\Verification;

use SheerID\Model\LastResponse;
use SheerID\Model\PersonInfo;

/**
 * @property string $programId
 * @property string $trackingId
 * @property string $personId
 * @property string $created
 * @property string $updated
 * @property LastResponse $lastResponse
 * @property PersonInfo $personInfo
 * @property int $docUploadRejectionCount
 * @property string[] $docUploadRejectionReasons
 * @property string $verificationMethod
 * @property string[] $approvingVerificationTypes
 */
class VerificationDetails extends \SheerID\Model\AbstractModel {
	/**
	 * @return string
	 */
	public function getProgramId() {
		return $this->programId;
	}

	/**
	 * @param string $programId
	 */
	public function setProgramId( $programId ) {
		$this->programId = $programId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTrackingId() {
		return $this->trackingId;
	}

	/**
	 * @param string $trackingId
	 */
	public function setTrackingId( $trackingId ) {
		$this->trackingId = $trackingId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPersonId() {
		return $this->personId;
	}

	/**
	 * @param string $personId
	 */
	public function setPersonId( $personId ) {
		$this->personId = $personId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param string $created
	 */
	public function setCreated( $created ) {
		$this->created = $created;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param string $updated
	 */
	public function setUpdated( $updated ) {
		$this->updated = $updated;

		return $this;
	}

	/**
	 * @return LastResponse
	 */
	public function getLastResponse() {
		return $this->lastResponse;
	}

	/**
	 * @param LastResponse $lastResponse
	 */
	public function setLastResponse( $lastResponse ) {
		$this->lastResponse = $lastResponse;

		return $this;
	}

	/**
	 * @return \SheerID\Model\PersonInfo
	 */
	public function getPersonInfo() {
		return $this->personInfo;
	}

	/**
	 * @param object $personInfo
	 */
	public function setPersonInfo( $personInfo ) {
		$this->personInfo = $personInfo;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDocUploadRejectionCount() {
		return $this->docUploadRejectionCount;
	}

	/**
	 * @param int $docUploadRejectionCount
	 */
	public function setDocUploadRejectionCount( $docUploadRejectionCount ) {
		$this->docUploadRejectionCount = $docUploadRejectionCount;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getDocUploadRejectionReasons() {
		return $this->docUploadRejectionReasons;
	}

	/**
	 * @param string[] $docUploadRejectionReasons
	 */
	public function setDocUploadRejectionReasons( $docUploadRejectionReasons ) {
		$this->docUploadRejectionReasons = $docUploadRejectionReasons;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getVerificationMethod() {
		return $this->verificationMethod;
	}

	/**
	 * @param string $verificationMethod
	 */
	public function setVerificationMethod( $verificationMethod ) {
		$this->verificationMethod = $verificationMethod;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getApprovingVerificationTypes() {
		return $this->approvingVerificationTypes;
	}

	/**
	 * @param string[] $approvingVerificationTypes
	 */
	public function setApprovingVerificationTypes( $approvingVerificationTypes ) {
		$this->approvingVerificationTypes = $approvingVerificationTypes;

		return $this;
	}

}