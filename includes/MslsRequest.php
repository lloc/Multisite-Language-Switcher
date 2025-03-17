<?php declare( strict_types=1 );

namespace lloc\Msls;

class MslsRequest {

	/**
	 * @return array<int, int>
	 */
	public static function get_config( string $name ): array {
		$config = MslsFields::CONFIG[ $name ] ?? null;

		if ( null === $config ) {
			throw new \InvalidArgumentException( 'Invalid field name' );
		}

		return $config;
	}

	public static function has_var( string $var_name, ?int $input_type = null ): bool {
		if ( null === $input_type ) {
			try {
				list( $input_type, ) = self::get_config( $var_name );
			} catch ( \InvalidArgumentException $e ) {
				return false;
			}
		}

		return filter_has_var( $input_type, $var_name );
	}

	/**
	 * @return mixed
	 */
	public static function get_var( string $var_name, ?int $input_type = null ) {
		try {
			list($type, $filter) = self::get_config( $var_name );
		} catch ( \InvalidArgumentException $e ) {
			return null;
		}

		$type = $input_type ?? $type;
		if ( in_array( $type, array( INPUT_POST, INPUT_GET, INPUT_COOKIE, INPUT_ENV, INPUT_SERVER ), true ) ) {
			return filter_input( $type, $var_name, $filter );
		}

		return null;
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

	/**
	 * Gets the request values for a list of keys.
	 *
	 * It will treat each key as a string and will return an array with every key as index and the value as a sanitized string.
	 *
	 * @param string[] $keys
	 * @param mixed    $default
	 *
	 * @return array<string, mixed>
	 */
	public static function get_request( array $keys, $default = '' ): array {
		$values = array();

		foreach ( $keys as $key ) {
			list( , $filter ) = self::get_config( $key );
			$values[ $key ]   = $default;

			if ( filter_has_var( INPUT_POST, $key ) ) {
				$values[ $key ] = filter_input( INPUT_POST, $key, $filter );
			} elseif ( filter_has_var( INPUT_GET, $key ) ) {
				$values[ $key ] = filter_input( INPUT_GET, $key, $filter );
			}
		}

		return $values;
	}
}
