<?php

namespace SheerID\Service;

use SheerID\Model\Verification\VerificationDetails;

class VerificationDetailsService extends AbstractService {

	protected $namespace = '/rest/v2/verification';

	/**
	 * @param $id
	 * @param $params
	 * @param $opts
	 *
	 * @return VerificationDetails
	 */
	public function retrieve( $id, $params = null, $opts = null ) {
		return $this->request( 'GET', $this->buildPath( '/%s/details', $id ), VerificationDetails::class, $params, $opts );
	}


}