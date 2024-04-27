<?php
/**
 * MslsGetSet
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Generic class for overloading properties
 *
 * @example https://gist.githubusercontent.com/lloc/2c232cef3f910acf692f/raw/f4eb70f4b1f8dc90c212d85d65af40c6604a32b9/MslsGetSet.php
 *
 * @package Msls
 */
class MslsGetSet extends MslsRegistryInstance {

	/**
	 * Generic container for all properties of an instance
	 * @var array $arr
	 */
	protected $arr = [];

	/**
	 * Overloads the set method.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set( $key, $value ) {
		$this->arr[ $key ] = $value;

		if ( empty( $this->arr[ $key ] ) ) {
			unset( $this->arr[ $key ] );
		}
	}

	/**
	 * Overloads the get method.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->arr[ $key ] ?? null;
	}

	/**
	 * Overloads the isset method.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->arr[ $key ] );
	}

	/**
	 * Overloads the unset method.
	 *
	 * @param string $key
	 */
	public function __unset( $key ) {
		if ( isset( $this->arr[ $key ] ) ) {
			unset( $this->arr[ $key ] );
		}
	}

	/**
	 * Resets the properties container to an empty array.
	 *
	 * @return MslsGetSet
	 */
	public function reset() {
		$this->arr = [];

		return $this;
	}

	/**
	 * Checks if the array has a non-empty item with the specified key name.
	 *
	 * This is method is similar to the overloaded __isset-method since
	 * __set cleans empty properties, but I use for example
	 *
	 *     $obj->has_value( $temp )
	 *
	 * and not
	 *
	 *     isset( $obj->$temp )
	 *
	 * which is the same but in my opinion a little bit ugly.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_value( $key ) {
		return ! empty( $this->arr[ $key ] );
	}

	/**
	 * Checks if the properties-container is empty.
	 *
	 * @return bool
	 */
	public function is_empty() {
		return empty( $this->arr );
	}

	/**
	 * Gets the complete properties-container as an array.
	 *
	 * @return array
	 */
	public function get_arr() {
		return $this->arr;
	}

}
