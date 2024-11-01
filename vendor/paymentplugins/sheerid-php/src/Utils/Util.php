<?php

namespace SheerID\Utils;

class Util {

	const TRIM_CHARS = '/\\';

	/**
	 * Trims "/" from the beginning and end of a string.
	 *
	 * @param $path
	 *
	 * @return void
	 */
	public static function trimPath( $path ) {
		return rtrim( ltrim( $path, self::TRIM_CHARS ), self::TRIM_CHARS );
	}

	/**
	 * Deeply merges two arrays
	 *
	 * @param $opts
	 * @param $defaults
	 *
	 * @return void
	 */
	public static function mergeDeep( $defaults, $opts ) {
		foreach ( $defaults as $key => $value ) {
			if ( ! isset( $opts[ $key ] ) ) {
				$opts[ $key ] = $value;
			} else {
				if ( \is_array( $opts[ $key ] ) ) {
					$opts[ $key ] = self::mergeDeep( $opts[ $key ], $value );
				}
			}
		}

		return $opts;
	}

	public static function buildResponseInstance( $response, $clazz = null ) {
		if ( self::isList( $clazz ) ) {
			return $clazz[0]::constructFrom( $response, $clazz[1] );
		}

		return $clazz::constructFrom( $response );
	}

	public static function constructModelInstance( $values, $key = null ) {
		if ( $key && isset( ModelTypes::$types[ $key ] ) ) {
			return ModelTypes::$types[ $key ]::constructFrom( $values );
		}
		if ( self::isList( $values ) ) {
			$list = [];
			foreach ( $values as $v ) {
				$list[] = self::constructModelInstance( $v );
			}

			return $list;
		}
		if ( \is_array( $values ) ) {
			$response = new \stdClass();
			foreach ( $values as $key => $value ) {
				if ( isset( ModelTypes::$types[ $key ] ) ) {
					$response->{$key} = ModelTypes::$types[ $key ]::constructFrom( $value );
				} else {
					$response->{$key} = self::constructModelInstance( $value );
				}
			}

			return $response;
		}

		return $values;
	}

	public static function isList( $value ) {
		if ( array() === $value ) {
			return true;
		}
		if ( \is_array( $value ) ) {
			if ( array_keys( $value ) === \range( 0, \count( $value ) - 1 ) ) {
				return true;
			}
		}

		return false;
	}

}