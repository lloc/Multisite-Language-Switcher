<?php
/**
 * MslsTaxonomy
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Stores the language input from post
 * 
 *     $arr = array(
 *         'it'    => 1,
 *         'de_DE' => 2,
 *     );
 *     $obj = new MslsLanguageArray( $arr );
 *     $val = $obj->get_val( 'it' );         // 1 == $val
 *     $val = $obj->get_val( 'fr_FR' );      // 0 == $val 
 *     $val = $obj->get_arr();               // array( 'it' => 1, 'de_DE' => 2 ) == $val
 *     $val = $obj->get_arr( 'de_DE' ) );    // array( 'it' => 1 ) == $val
 * 
 * @package Msls
 */
class MslsLanguageArray {

	/**
	 * Generic container
	 * @var array
	 */
	protected $arr;

	/**
	 * Constructor
	 * @param array $arr
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
	 * @param string $key
	 * @param mixed $value
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
	 * @param string $key
	 * @return int
	 */
	public function get_val( $key ) {
		return( isset( $this->arr[ $key ] ) ? $this->arr[ $key ] : 0 );
	}

	/**
	 * Get the filtered array without the specified element
	 * @param string $key
	 * @return array
	 */
	public function get_arr( $key = '' ) {
		$arr = $this->arr;
		if ( isset( $arr[ $key ] ) ) {
			unset( $arr[ $key ] );
		}
		return $arr;
	}

}
