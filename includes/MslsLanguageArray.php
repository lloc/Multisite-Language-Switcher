<?php

/**
 * Stores the language input from post
 *
 * @package Msls
 * @subpackage Main
 */
class MslsLanguageArray {

    /**
     * @var array $arr
     */
    protected $arr;

    /**
     * Constructor
     * 
     * @param array $arr
     */
    public function __construct( array $arr = array() ) {
        $this->arr = array_filter( $arr );
    }

    /**
     * Sets a key-value-pair
     * - $key must be a string of length >= 2
     * - $value must be an integer > 0  
     * 
     * @param string $key
     * @param mixed $value
     */
    public function set( $key, $value ) {
        $value = intval( $value ); 
        if ( strlen( $key ) >= 2 && $value > 0 )
            $this->arr[$key] = intval( $value );
    }

    /**
     * Gets the filtered array without the specified element
     * 
     * @param string $key
     * @return array
     */
    public function filter( $key ) {
        $arr = $this->arr;
        if ( isset( $arr[$key] ) )
            unset( $arr[$key] );
        return $arr;
    }

    /**
     * Gets the specified element of the array
     * 
     * @param string $key
     * @return int
     */
    public function get( $key ) {
        return( isset( $this->arr[$key] ) ? $this->arr[$key] : 0 );
    }

    /**
     * Gets the entries which are obsolete now
     * 
     * @param array $arr
     */
    public function obsolete( array $arr ) {
        return array_keys( array_diff_key( $arr, $this->arr ) );
    }

}

?>
