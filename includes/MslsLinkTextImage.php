<?php

/**
 * LinkTextImage
 * 
 * @package Msls
 * @subpackage Link
 */
class MslsLinkTextImage extends MslsLink {

    /**
     * @var string
     */
    protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

    /**
     * Get the description
     * 
     * @return string
     */
    static function get_description() {
        return __( 'Description and flag', 'msls' );
    }

}
