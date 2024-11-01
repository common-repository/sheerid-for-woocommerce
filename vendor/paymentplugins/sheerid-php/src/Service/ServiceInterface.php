<?php

namespace SheerID\Service;

interface ServiceInterface {

	public function request( $method, $path, $clazz = null, $params = null, $opts = null );
}