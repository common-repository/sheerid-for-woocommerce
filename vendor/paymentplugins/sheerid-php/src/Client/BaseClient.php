<?php

namespace SheerID\Client;

use SheerID\Service\Factory\BaseServiceFactory;
use SheerID\Service\LoginService;
use SheerID\Service\ProgramService;
use SheerID\Service\SegmentService;
use SheerID\Service\VerificationService;
use SheerID\Service\VerificationDetailsService;
use SheerID\Service\WebhookService;

/**
 * @property VerificationService $verifications
 * @property VerificationDetailsService $verificationDetails;
 * @property ProgramService $programs
 * @property SegmentService $segments
 * @property WebhookService $webhooks
 * @property LoginService $login
 */
class BaseClient extends AbstractClient {

	private $serviceFactory;

	public function __get( $name ) {
		if ( ! $this->serviceFactory ) {
			$this->serviceFactory = new BaseServiceFactory( $this );
		}

		return $this->serviceFactory->get( $name );
	}

}