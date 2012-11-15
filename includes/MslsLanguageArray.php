<?php
/**
 * MslsTaxonomy
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Stores the language input from post
 * @package Msls
 * @subpackage Main
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
        foreach ( $arr as $key => $value ) 
            $this->set( $key, $value );
    }

    /**
     * set
     * 
     * Sets a key-value-pair
     * - $key must be a string of length >= 2
     * - $value must be an integer > 0  
     * @param string $key
     * @param mixed $value
     * @return MslsLanguageArray
     */
    public function set( $key, $value ) {
        $value = intval( $value );
        if ( 2 <= strlen( $key ) && 0 < $value )
            $this->arr[$key] = $value;
        return $this;
    }


    /**
     * get_arr
     * 
     * Gets the value of the requested item
     * @param string $key
     * @return int
     */
    public function get_val( $key ) {
        return( isset( $this->arr[$key] ) ? $this->arr[$key] : 0 );
    }

    /**
     * get_val
     * 
     * Gets the filtered array without the specified element
     * @param string $key
     * @return array
     */
    public function get_arr( $key = '' ) {
        $arr = $this->arr;
        if ( isset( $arr[$key] ) )
            unset( $arr[$key] );
        return $arr;
    }

}
