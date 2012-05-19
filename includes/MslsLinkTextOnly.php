<?php

/**
 * LinkTextOnly
 * 
 * @package Msls
 * @subpackage Link
 */
class MslsLinkTextOnly extends MslsLink {

    /**
     * @var string
     */
    protected $format_string = '{txt}';

    /**
     * Get the description
     * 
     * @return string
     */
    public static function get_description() {
        return __( 'Description only', 'msls' );
    }

}
