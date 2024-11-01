<?php

namespace SheerID\Service;

use SheerID\Model\Collection;
use SheerID\Model\Program;

class ProgramService extends AbstractService {

	protected $namespace = '/rest/oem';

	/**
	 * @param $id
	 * @param $params
	 * @param $opts
	 *
	 * @return Program
	 */
	public function retrieve( $id, $params = null, $opts = null ) {
		return $this->request( 'GET', $this->buildPath( '/program/%s', $id ), Program::class, $params, $opts );
	}

	/**
	 * @param $params
	 * @param $opts
	 *
	 * @return Program
	 */
	public function create( $params, $opts = null ) {
		return $this->post( $this->buildPath( '/program', ), Program::class, $params, $opts );
	}

	public function update( $id, $params = null, $opts = null ) {
		return $this->post( $this->buildPath( '/program/%s', $id ), Program::class, $params, $opts );
	}

	/**
	 * @param $id
	 * @param $name
	 * @param $opts
	 *
	 * @return Program
	 */
	public function updateName( $id, $name, $opts = null ) {
		return $this->post( $this->buildPath( '/program/%s/name', $id ), Program::class, [ 'name' => $name ], $opts );
	}

	/**
	 * @param $id
	 * @param $params
	 * @param $opts
	 *
	 * @return Program
	 */
	public function updateAudience( $id, $params, $opts = null ) {
		return $this->post( $this->buildPath( '/program/%s/audience', $id ), Program::class, $params, $opts );
	}

	public function archive( $id, $opts = null ) {
		return $this->post( $this->buildPath( '/program/%s/archive', $id ), Program::class, null, $opts );
	}

	public function launch( $id ) {
		return $this->post( $this->buildPath( '/program/%s/launch', $id ), Program::class, null, null );
	}

	public function unlaunch( $id ) {
		return $this->post( $this->buildPath( '/program/%s/unlaunch', $id ), Program::class, null, null );
	}

	public function all() {
		return $this->get( $this->buildPath( '/program' ), [ Collection::class, Program::class ], null );
	}

}