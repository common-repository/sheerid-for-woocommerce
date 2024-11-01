<?php

namespace SheerID\Http;

use SheerID\Platform\PlatformInterface;
use SheerID\Platform\WordPress\WordPressPlatform;

class HttpFactory {

	private static $platforms = [
		WordPressPlatform::class
	];

	public static function getDefaultInstance() {
		foreach ( self::$platforms as $clazz ) {
			/**
			 * @var PlatformInterface $platform
			 */
			$platform = new $clazz();
			if ( $platform->isActive() ) {
				return $platform->getHttpClient();
			}
		}

		return null;
	}
}