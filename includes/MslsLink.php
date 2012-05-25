<?php

/**
 * Link
 * 
 * @package Msls
 * @subpackage Link
 */
class MslsLink extends MslsGetSet {

    /**
     * @var array
     */
    protected $args = array();

    /**
     * @var string
     */
    protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

    /**
     * Get link types
     *
     * @return array
     */
    public static function get_types() {
        return array( 
            '0' => 'MslsLink',
            '1' => 'MslsLinkTextOnly',
            '2' => 'MslsLinkImageOnly',
            '3' => 'MslsLinkTextImage',
        );
    }

    /**
     * Get link description
     *
     * @return string
     */
    public static function get_description() {
        return __( 'Flag and description', 'msls' );
    }

    /**
     * Get array with all link descriptions
     *
     * @return array
     */
    public static function get_types_description() {
        $temp = array();
        foreach ( self::get_types() as $key => $class ) {
            $temp[$key] = call_user_func(
                array( $class, 'get_description' )
            );
        }
        return $temp;
    }
    
    /**
     * Factory: Create a specific instance of MslsLink
     *
     * @param int $display
     * @return MslsLink
     */
    public static function create( $display ) {
        if ( has_filter( 'msls_link_create' ) ) {
            $obj = apply_filters( 'msls_link_create', $display );
            if ( is_subclass_of( $obj, 'MslsLink' ) )
                return $obj;
        }
        $types = self::get_types();
        if ( !in_array( $display, array_keys( $types ), true ) )
            $display = 0;
        return new $types[$display];
    }

    /**
     * Handles the request to print the object
     */
    public function __toString() {
        $temp = array();
        foreach ( array_keys( $this->get_arr() ) as $key ) {
            $temp[] = '{' . $key . '}';
        }
        return str_replace(
            $temp,
            $this->get_arr(),
            $this->format_string
        );
    }

}
