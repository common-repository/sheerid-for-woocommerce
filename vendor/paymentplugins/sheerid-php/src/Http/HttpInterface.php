<?php

namespace SheerID\Http;

interface HttpInterface {

	public function request( $method, $url, $params, $headers );
}