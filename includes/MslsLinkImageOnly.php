<?php

/**
 * MslsLinkImageOnly
 * 
 * @package Msls
 */
class MslsLinkImageOnly extends MslsLink {

    /**
     * @var string
     */
    protected $format_string = '<img src="{src}" alt="{alt}"/>';

    /**
     * Get the description
     * 
     * @return string
     */
    static function get_description() {
        return __( 'Flag only', 'msls' );
    }

}

?>
