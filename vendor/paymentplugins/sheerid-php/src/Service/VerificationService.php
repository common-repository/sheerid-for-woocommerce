<?php

namespace SheerID\Service;

use SheerID\Model\Metadata;
use SheerID\Model\Verification\BaseVerification;

class VerificationService extends AbstractService {

	protected $namespace = '/rest/v2/verification';

	/**
	 * @param $params
	 *
	 * @return BaseVerification
	 */
	public function create( $params ) {
		return $this->request( 'POST', $this->buildPath( '' ), BaseVerification::class, $params );
	}

	/**
	 * @param $id
	 * @param $metadata
	 *
	 * @return Metadata
	 */
	public function updateMetadata( $id, $metadata ) {
		return $this->request( 'PUT', $this->buildPath( '/%s/metadata', $id ), Metadata::class, $metadata );
	}
}