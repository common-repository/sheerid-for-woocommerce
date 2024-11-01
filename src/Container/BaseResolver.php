<?php

namespace WooCommerce\SheerID\Container;

class BaseResolver {

	private $callback;

	private $singleton;

	private $instance;

	public function __construct( $callback, $singleton = true ) {
		$this->callback  = $callback;
		$this->singleton = $singleton;
	}

	public function get( $container ) {
		if ( $this->instance ) {
			return $this->instance;
		}
		$callback = $this->callback;
		if ( \is_callable( $callback ) ) {
			$instance = $callback( $container );
		} else {
			$instance = $callback;
		}
		if ( $this->singleton ) {
			$this->instance = $instance;
		}

		return $instance;
	}

}