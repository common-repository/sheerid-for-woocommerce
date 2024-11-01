<?php

namespace SheerID\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $idExtended
 * @property string $source
 */
class Organization extends AbstractModel {

	const MODEL_TYPE = 'organization';

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
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
	public function getIdExtended() {
		return $this->idExtended;
	}

	/**
	 * @param string $idExtended
	 */
	public function setIdExtended( $idExtended ) {
		$this->idExtended = $idExtended;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * @param string $source
	 */
	public function setSource( $source ) {
		$this->source = $source;

		return $this;
	}


}