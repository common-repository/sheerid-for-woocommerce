<?php

namespace SheerID\Model;

/**
 * @property array $tags
 */
class CustomerTagging extends AbstractModel {

	const MODEL_TYPE = 'customerTagging';

	/**
	 * @return array
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * @param array $tags
	 */
	public function setTags( $tags ) {
		$this->tags = $tags;

		return $this;
	}


}