<?php

/**
 * Generic class for overloading properties
 *
 * <code>
 * $obj = new MslsGetSet;
 * $obj->test = 'This is just a test';
 * echo $obj->test;
 * </code>
 * 
 * @package Msls
 * @subpackage Main
 */
class MslsGetSet {

    /**
     * @var array
     */
    protected $arr = array();

    /**
     * "Magic" set arg
     *
     * @param mixed $key
     * @param mixed $value
     */
    final public function __set( $key, $value ) {
        $this->arr[$key] = $value;
        if ( empty( $this->arr[$key] ) )
            unset( $this->arr[$key] );
    }

    /**
     * "Magic" get arg
     *
     * @param mixed $key
     * @return mixed
     */
    final public function __get( $key ) {
        return isset( $this->arr[$key] ) ? $this->arr[$key] : null;
    }

    /**
     * "Magic" isset
     *
     * @param mixed $key
     * @return bool
     */
    final public function __isset( $key ) {
        return isset( $this->arr[$key] );
    }

    /**
     * "Magic" unset
     *
     * @param mixed $key
     */
    final public function __unset( $key ) {
        if ( isset( $this->arr[$key] ) )
            unset( $this->arr[$key] );
    }

    /**
     * Reset all
     */
    public function reset() {
        $this->arr = array();
    }

    /**
     * Check if the array has an non empty item with the specified key
     * 
     * @param string $key
     * @return bool
     */ 
    public function has_value( $key ) {
        return( !empty( $this->arr[$key] ) );
    }

    /**
     * Check if the array is not empty
     * 
     * @return bool
     */ 
    public function is_empty() {
        return( empty( $this->arr ) );
    }

    /**
     * Get args-array
     *
     * @return array
     */
    final public function get_arr() {
        return $this->arr;
    }

}
