<?php
/**
 * MslsLanguageArray
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Stores the language input from post
 * @example https://gist.githubusercontent.com/lloc/2c232cef3f910acf692f/raw/c78a78b42cb4c9e97a118523f7497f02b838a2ee/MslsLanguageArray.php
 * @package Msls
 */
class MslsLanguageArray {

	/**
	 * Generic container
	 * @var array<string, int>
	 */
	protected $arr;

	/**
	 * Constructor
	 * @param array<string, int> $arr
	 */
	public function __construct( array $arr = [] ) {
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
	 * @param int $value
	 *
	 * @return MslsLanguageArray
	 */
	public function set( string $key, int $value ): MslsLanguageArray {
		if ( 2 <= strlen( $key ) && 0 < $value ) {
			$this->arr[ $key ] = $value;
		}

		return $this;
	}


	/**
	 * Get the value of the element with the specified key
	 *
	 * @param string $key
	 * @return int
	 */
	public function get_val( string $key ): int {
		return $this->arr[ $key ] ?? 0;
	}

	/**
	 * Get the filtered array without the specified element
	 *
	 * @param string $key
	 *
	 * @return array<string, int>
	 */
	public function get_arr( string $key = '' ) {
		$arr = $this->arr;

		if ( isset( $arr[ $key ] ) ) {
			unset( $arr[ $key ] );
		}

		return $arr;
	}

}
