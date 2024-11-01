<?php

namespace SheerID\Model;

/**
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $birthDate
 * @property Metadata $metadata
 * @property Organization $organization
 */
class PersonInfo extends AbstractModel {

	const MODEL_TYPE = 'personInfo';

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @param string $firstName
	 */
	public function setFirstName( $firstName ) {
		$this->firstName = $firstName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 */
	public function setLastName( $lastName ) {
		$this->lastName = $lastName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail( $email ) {
		$this->email = $email;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBirthDate() {
		return $this->birthDate;
	}

	/**
	 * @param string $birthDate
	 */
	public function setBirthDate( $birthDate ) {
		$this->birthDate = $birthDate;

		return $this;
	}

	/**
	 * @return Metadata
	 */
	public function getMetadata() {
		return $this->metadata;
	}

	/**
	 * @param Metadata $metadata
	 */
	public function setMetadata( $metadata ) {
		$this->metadata = $metadata;

		return $this;
	}

	/**
	 * @return Organization
	 */
	public function getOrganization() {
		return $this->organization;
	}

	/**
	 * @param Organization $organization
	 */
	public function setOrganization( $organization ) {
		$this->organization = $organization;

		return $this;
	}

}