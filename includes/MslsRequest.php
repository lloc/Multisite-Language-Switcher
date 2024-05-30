<?php

namespace lloc\Msls;

class MslsRequest {


	public static function get_config( $name ): array {
		$config = MslsFields::CONFIG[ $name ] ?? null;

		if ( null === $config ) {
			throw new \InvalidArgumentException( 'Invalid field name' );
		}

		return $config;
	}

	public static function has_var( string $name ): bool {
		try {
			list($type, ) = self::get_config( $name );
		} catch ( \InvalidArgumentException $e ) {
			return false;
		}

		return filter_has_var( $type, $name );
	}

	public static function get_var( string $name ) {
		try {
			list($type, $filter) = self::get_config( $name );
		} catch ( \InvalidArgumentException $e ) {
			return null;
		}

		return filter_input( $type, $name, $filter );
	}

	/**
	 * @param string $name
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public static function get( string $name, $default ) {
		return self::has_var( $name ) ? self::get_var( $name ) : $default;
	}

	/**
	 * @param string[] $keys
	 *
	 * @return bool
	 */
	public static function isset( array $keys ): bool {
		foreach ( $keys as $key ) {
			if ( ! self::has_var( $key ) ) {
				return false;
			}
		}

		return true;
	}
}
