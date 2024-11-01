<?php

namespace SheerID\Model;

class Collection extends AbstractModel implements \Countable, \ArrayAccess {

	public function offsetExists( $offset ): bool {
		return isset( $this->_values[ $offset ] );
	}

	public function offsetGet( $offset ): mixed {
		return $this->_values[ $offset ];
	}

	public function offsetSet( $offset, $value ): void {
		$this->_values[ $offset ] = $value;
	}

	public function offsetUnset( $offset ): void {
		unset( $this->_values[ $offset ] );
	}

	public function count(): int {
		return \count( $this->_values );
	}

	public function add( $item ) {
		$this->_values[] = $item;
	}

	public function items() {
		return $this->_values;
	}

	public static function constructFrom( $values, $type = null ) {
		$instance = new static();
		foreach ( $values as $value ) {
			$instance->add( $type::constructFrom( $value ) );
		}

		return $instance;
	}
}