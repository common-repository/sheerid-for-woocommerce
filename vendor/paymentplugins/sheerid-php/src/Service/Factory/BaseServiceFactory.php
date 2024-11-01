<?php

namespace SheerID\Service\Factory;

use SheerID\Service\LoginService;
use SheerID\Service\ProgramService;
use SheerID\Service\SegmentService;
use SheerID\Service\VerificationDetailsService;
use SheerID\Service\VerificationService;
use SheerID\Service\WebhookService;

class BaseServiceFactory extends AbstractServiceFactory {

	public static $classMappings = [
		'verifications'       => VerificationService::class,
		'verificationDetails' => VerificationDetailsService::class,
		'programs'            => ProgramService::class,
		'segments'            => SegmentService::class,
		'webhooks'            => WebhookService::class,
		'login'               => LoginService::class
	];

	protected function getServiceClass( $key ) {
		return self::$classMappings[ $key ] ?? null;
	}

}