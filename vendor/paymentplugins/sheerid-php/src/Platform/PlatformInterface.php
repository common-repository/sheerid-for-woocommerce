<?php

namespace SheerID\Platform;

interface PlatformInterface {

	public function isActive();

	public function getHttpClient();
}