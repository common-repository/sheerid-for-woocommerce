<?php

namespace WooCommerce\SheerID\Admin\MetaBox\Traits;

trait ProgramTrait {

	/**
	 * @return mixed
	 * @todo - Use the SheerID client and fetch the programs
	 */
	protected function get_program_options() {
		$programs = $this->client->programs->all();
		if ( ! is_wp_error( $programs ) ) {
			return \array_reduce( $programs->items(), function ( $carry, $program ) {
				$carry[ $program->getId() ] = $program->getName();

				return $carry;
			} );
		}

		return [];
	}

}