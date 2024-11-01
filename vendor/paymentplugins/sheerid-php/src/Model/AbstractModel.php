<?php

namespace SheerID\Model;

use SheerID\Utils\Util;

/**
 * Model class that all models should extend
 */
abstract class AbstractModel implements \JsonSerializable {

	const MODEL_TYPE = '';

	const MODEL_TYPES = '';

	protected $_values = [];

	public function __construct() {

	}

	public function __get( $name ) {
		if ( isset( $this->{$name} ) ) {
			return $this->_values[ $name ];
		}

		return null;
	}

	public function __set( $name, $value ) {
		$this->_values[ $name ] = $value;
	}

	public function __isset( $name ) {
		return isset( $this->_values[ $name ] );
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return $this->_values;
	}

	/**
	 * @param $values
	 *
	 * @return void
	 */
	public static function constructFrom( $values ) {
		$instance = new static();
		if ( \is_array( $values ) ) {
			foreach ( $values as $key => $value ) {
				$instance->{$key} = Util::constructModelInstance( $value, $key );
			}
		}

		return $instance;
	}
}