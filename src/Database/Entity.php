<?php

namespace WooCommerce\SheerID\Database;

abstract class Entity implements \JsonSerializable {

	protected $id;

	protected $data_store;

	protected $data = [];

	protected $changes = [];

	protected $object_read = false;

	protected $object_type;

	public function __construct( $id = 0 ) {
		if ( $id ) {
			if ( \is_numeric( $id ) && $id > 0 ) {
				$this->set_id( $id );
			} elseif ( \is_object( $id ) ) {
				$this->set_props( $id );
				$this->set_object_read( true );
			} else {
				$this->set_object_read( true );
			}
		}
		$this->data_store = \WC_Data_Store::load( $this->get_datastore_id() );

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->data_store->read( $this );
		}
	}

	abstract public function get_datastore_id();

	public function set_object_read( $bool ) {
		$this->object_read = true;
	}

	public function set_id( $id ) {
		$this->id = $id;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_changes() {
		return $this->changes;
	}

	public function set_props( $props ) {
		foreach ( $props as $prop => $value ) {
			$method = "set_{$prop}";
			if ( \method_exists( $this, $method ) ) {
				$this->{$method}( $value );
			}
		}
	}

	public function delete() {
		if ( $this->get_id() ) {
			$this->data_store->delete( $this );
		}
	}

	public function set_prop( $key, $value ) {
		if ( \array_key_exists( $key, $this->data ) ) {
			if ( $this->object_read && ( $this->data[ $key ] !== $value || \array_key_exists( $key, $this->changes ) ) ) {
				$this->changes[ $key ] = $value;
			} else {
				$this->data[ $key ] = $value;
			}
		}
	}

	public function set_date_prop( $key, $value ) {
		if ( $value === '0000-00-00 00:00:00' ) {
			$this->set_prop( $key, $value );
		} else {
			$this->set_prop( $key, \DateTime::createFromFormat( 'Y-m-d H:i:s', $value ) );
		}
	}

	public function set_bool_prop( $key, $value ) {
		if ( ! is_bool( $value ) ) {
			$value = \wc_string_to_bool( $value );
		}
		$this->set_prop( $key, $value );
	}

	public function get_prop( $key ) {
		$value = null;
		if ( \array_key_exists( $key, $this->changes ) ) {
			$value = $this->changes[ $key ];
		} elseif ( \array_key_exists( $key, $this->data ) ) {
			$value = $this->data[ $key ];
		}

		return $value;
	}

	public function save() {
		if ( $this->get_id() ) {
			$this->data_store->update( $this );
			do_action( 'woocommerce_sheerid_' . $this->object_type . '_updated', $this->get_id(), $this );
		} else {
			$this->data_store->create( $this );
			do_action( 'woocommerce_sheerid_' . $this->object_type . '_created', $this->get_id(), $this );
		}
	}

	#[\ReturnTypeWillChange]
	/**
	 * @return array|mixed
	 *
	 */
	public function jsonSerialize() {
		return [ 'id' => $this->id ] + $this->data;
	}

}