<?php

namespace SheerID\Model;

/**
 * @property string $iconSrc
 * @property string $backgroundColor
 */
class DisplayInfo extends AbstractModel {

	const MODEL_TYPE = 'displayInfo';

	/**
	 * @return string
	 */
	public function getIconSrc() {
		return $this->iconSrc;
	}

	/**
	 * @param string $iconSrc
	 */
	public function setIconSrc( $iconSrc ) {
		$this->iconSrc = $iconSrc;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBackgroundColor() {
		return $this->backgroundColor;
	}

	/**
	 * @param string $backgroundColor
	 */
	public function setBackgroundColor( $backgroundColor ) {
		$this->backgroundColor = $backgroundColor;

		return $this;
	}



}