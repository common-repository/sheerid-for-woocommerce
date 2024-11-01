<?php

namespace SheerID\Client;

interface ClientInterface {

	/**
	 * @param $method - The request method. Example: "get", "post", "put", "delete"
	 * @param $path - The rest path that will be appended to the base path
	 * @param $clazz - The class that will be returned to the requesting code.
	 * @param $opts - An array of options which can override the default options
	 *
	 * @return mixed
	 */
	public function request( $method, $path, $clazz = null, $params = null, $opts = null );

	public function getBasePath();
}