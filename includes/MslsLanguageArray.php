<?php

namespace lloc\Msls;

/**
 * Stores the language input from post
 *
 * @example https://gist.githubusercontent.com/lloc/2c232cef3f910acf692f/raw/c78a78b42cb4c9e97a118523f7497f02b838a2ee/MslsLanguageArray.php
 * @package Msls
 */
class MslsLanguageArray {

	/**
	 * @var array<string, int>
	 */
	protected array $arr;

	/**
	 * Constructor
	 *
	 * @param array<string, mixed> $arr
	 */
	public function __construct( array $arr = array() ) {
		foreach ( $arr as $key => $value ) {
			$this->set( $key, $value );
		}
	}

	/**
	 * Set a key-value-pair
	 * - $key must be a string of length >= 2
	 * - $value must be an integer > 0
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return MslsLanguageArray
	 */
	public function set( $key, $value ) {
		$value = (int) $value;
		if ( 2 <= strlen( $key ) && 0 < $value ) {
			$this->arr[ $key ] = $value;
		}

		return $this;
	}


	/**
	 * Get the value of the element with the specified key
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	public function get_val( $key ) {
		return $this->arr[ $key ] ?? 0;
	}

	/**
	 * Get the filtered array without the specified element
	 *
	 * @param string $key
	 *
	 * @return array<string, int>
	 */
	public function get_arr( string $key = '' ): array {
		$arr = $this->arr;

		if ( isset( $arr[ $key ] ) ) {
			unset( $arr[ $key ] );
		}

		return $arr;
	}
}
