<?php

namespace SheerID\Model;

/**
 * @property string $font
 * @property string $backgroundColor
 * @property stsring $primaryFontColor
 * @property string $buttonColor
 * @property string $buttonFontColor
 * @property string $linkColor
 * @property string $h1FontColor
 * @property string $helperFontColor
 */
class CustomCssRequest extends AbstractModel {

	const MODEL_TYPE = 'customCssRequest';

	/**
	 * @return string
	 */
	public function getFont() {
		return $this->font;
	}

	/**
	 * @param string $font
	 */
	public function setFont( $font ) {
		$this->font = $font;

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

	/**
	 * @return stsring
	 */
	public function getPrimaryFontColor() {
		return $this->primaryFontColor;
	}

	/**
	 * @param stsring $primaryFontColor
	 */
	public function setPrimaryFontColor( $primaryFontColor ) {
		$this->primaryFontColor = $primaryFontColor;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getButtonColor() {
		return $this->buttonColor;
	}

	/**
	 * @param string $buttonColor
	 */
	public function setButtonColor( $buttonColor ) {
		$this->buttonColor = $buttonColor;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getButtonFontColor() {
		return $this->buttonFontColor;
	}

	/**
	 * @param string $buttonFontColor
	 */
	public function setButtonFontColor( $buttonFontColor ) {
		$this->buttonFontColor = $buttonFontColor;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLinkColor() {
		return $this->linkColor;
	}

	/**
	 * @param string $linkColor
	 */
	public function setLinkColor( $linkColor ) {
		$this->linkColor = $linkColor;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getH1FontColor() {
		return $this->h1FontColor;
	}

	/**
	 * @param string $h1FontColor
	 */
	public function setH1FontColor( $h1FontColor ) {
		$this->h1FontColor = $h1FontColor;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getHelperFontColor() {
		return $this->helperFontColor;
	}

	/**
	 * @param string $helperFontColor
	 */
	public function setHelperFontColor( $helperFontColor ) {
		$this->helperFontColor = $helperFontColor;

		return $this;
	}


}