<?php

namespace SheerID\Service;

use SheerID\Model\LoginResponse;

class LoginService extends AbstractService {

	protected $namespace = '/rest/oem';

	/**
	 * @param $params
	 * @param $opts
	 *
	 * @return LoginResponse
	 */
	public function connect( $params, $opts = null ) {
		return $this->request( 'POST', $this->buildPath( '/login' ), LoginResponse::class, $params, $opts );
	}
}