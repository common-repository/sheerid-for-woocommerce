<?php

namespace SheerID\Platform\WordPress;

use SheerID\Platform\PlatformInterface;

class WordPressPlatform implements PlatformInterface {

	public function isActive() {
		return defined( 'WP_CONTENT_DIR' );
	}

	public function getHttpClient() {
		return new WordPressHttp();
	}
}