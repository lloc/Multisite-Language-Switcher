<?php
/**
 * MslsGetSet
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Generic class for overloading properties
 *
 * <code>
 * $obj = new MslsGetSet;
 * $obj->tmp = 'test';
 * $val = $obj->get_arr();          // array( 'tmp' => 'test' ) == $val
 * $val = $obj->has_value( 'tmp' ); // true == $val
 * $val = $obj->is_empty();         // false == $val
 * echo $obj->tmp;                  // prints 'test'
 * $obj->reset();
 * $val = $obj->get_arr();          // array() == $val
 * $val = $obj->has_value( 'tmp' ); // false == $val
 * $val = $obj->is_empty();         // true == $val
 * echo $obj->tmp;                  // prints null
 * </code>
 * @package Msls
 */
class MslsGetSet {

	/**
	 * A generic container for all properties of an instance
	 * @var array $arr
	 */
	protected $arr = array();

	/**
	 * Overloaded set-method 
	 * @param mixed $key
	 * @param mixed $value
	 */
	final public function __set( $key, $value ) {
		$this->arr[$key] = $value;
		if ( empty( $this->arr[$key] ) )
			unset( $this->arr[$key] );
	}

	/**
	 * Overloaded get-method
	 * @param mixed $key
	 * @return mixed
	 */
	final public function __get( $key ) {
		return( isset( $this->arr[$key] ) ? $this->arr[$key] : null );
	}

	/**
	 * Overloaded isset-method
	 * @param mixed $key
	 * @return bool
	 */
	final public function __isset( $key ) {
		return isset( $this->arr[$key] );
	}

	/**
	 * Overloaded unset-method
	 * @param mixed $key
	 */
	final public function __unset( $key ) {
		if ( isset( $this->arr[$key] ) )
			unset( $this->arr[$key] );
	}

	/**
	 * Reset the properties-container to an empty array
	 * @return MslsGetSet
	 */
	public function reset() {
		$this->arr = array();
		return $this;
	}

	/**
	 * Check if the array has an non empty item with the specified key
	 * 
	 * This is method is similar to the overloaded __isset-method since
	 * __set cleans empty properties but I use for example 
	 * $obj->has_value( $temp ) and not isset( $obj->$temp ) which is
	 * the same but a little bit ugly.
	 * @param string $key
	 * @return bool
	 */ 
	public function has_value( $key ) {
		return( !empty( $this->arr[$key] ) );
	}

	/**
	 * Check if the properties-container is empty
	 * @return bool
	 */ 
	public function is_empty() {
		return( empty( $this->arr ) );
	}

	/**
	 * Get the complete properties-container as an array
	 * @return array
	 */
	final public function get_arr() {
		return $this->arr;
	}

}
