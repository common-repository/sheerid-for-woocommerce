<?php

namespace WooCommerce\SheerID\Utils;

use WooCommerce\SheerID\Admin\Settings\APISettings;

class JWTUtil {

	public static function encode( $payload, $secret = '' ) {
		$payload = \wp_parse_args( $payload, [
			'iat' => time()
		] );
		if ( ! $secret ) {
			$secret = sheerid_wc_container()->get( APISettings::class )->get_access_token();
		}
		$segments[] = self::base64_urlencode( \wp_json_encode( [
			'alg'  => 'HS256',
			'type' => 'JWT'
		] ) );


		$segments[] = self::base64_urlencode( \wp_json_encode( $payload ) );

		$segments[] = self::base64_urlencode( hash_hmac( 'SHA256', implode( '.', $segments ), $secret, true ) );

		return \implode( '.', $segments );
	}

	public static function base64_urlencode( $data ) {
		return str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], base64_encode( $data ) ); //phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
	}

}