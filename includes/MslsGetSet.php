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
 * $obj->test = 'This is just a test';
 * echo $obj->test;
 * </code>
 * @package Msls
 * @subpackage Main
 */
class MslsGetSet {

	/**
	 * A generic container for all properties of an instance
	 * @var array $arr
	 */
	protected $arr = array();

	/**
	 * set (overloaded) 
	 * @param mixed $key
	 * @param mixed $value
	 */
	final public function __set( $key, $value ) {
		$this->arr[$key] = $value;
		if ( empty( $this->arr[$key] ) )
			unset( $this->arr[$key] );
	}

	/**
	 * get (overloaded)
	 * @param mixed $key
	 * @return mixed
	 */
	final public function __get( $key ) {
		return( isset( $this->arr[$key] ) ? $this->arr[$key] : null );
	}

	/**
	 * isset (overloaded)
	 * @param mixed $key
	 * @return bool
	 */
	final public function __isset( $key ) {
		return isset( $this->arr[$key] );
	}

	/**
	 * unset (overloaded)
	 * @param mixed $key
	 */
	final public function __unset( $key ) {
		if ( isset( $this->arr[$key] ) )
			unset( $this->arr[$key] );
	}

	/**
	 * reset
	 * 
	 * Reset the whole properties-container
	 * <code>
	 * $obj = new MslsGetSet;
	 * $obj->temp = 'test';
	 * $obj->reset();
	 * $val = $obj->get_arr(); // array() == $val
	 * </code>
	 * @return MslsGetSet
	 */
	public function reset() {
		$this->arr = array();
		return $this;
	}

	/**
	 * has_value
	 * 
	 * Check if the array has an non empty item with the specified key.
	 * This is method is similar to the overloaded __isset-method since
	 * __set cleans empty properties but I use for example 
	 * $obj->has_value( $temp ) and not isset( $obj->$temp ) which is
	 * the same but a little bit ugly.
	 * <code>
	 * $obj = new MslsGetSet;
	 * $val = $obj->has_value( 'temp' ); // false == $val
	 * $obj->temp = 'test';
	 * $val = $obj->has_value( 'temp' ); // true == $val
	 * </code>
	 * @param string $key
	 * @return bool
	 */ 
	public function has_value( $key ) {
		return( !empty( $this->arr[$key] ) );
	}

	/**
	 * is_empty
	 * 
	 * Check if the properties-container is empty
	 * <code>
	 * $obj = new MslsGetSet;
	 * $val = $obj->is_empty(); // true == $val
	 * $obj->temp = 'test';
	 * $val = $obj->is_empty(); // false == $val
	 * </code>
	 * @return bool
	 */ 
	public function is_empty() {
		return( empty( $this->arr ) );
	}

	/**
	 * get_arr
	 * 
	 * Get the complete properties-container as an array
	 * <code>
	 * $obj = new MslsGetSet;
	 * $obj->temp = 'test';
	 * $val = $obj->get_arr(); // array( 'temp' => 'test' ) == $val
	 * </code>
	 * @return array
	 */
	final public function get_arr() {
		return $this->arr;
	}

}
