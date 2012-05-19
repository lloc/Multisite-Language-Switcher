<?php

/**
 * OptionsTaxTerm
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsTaxTerm extends MslsOptionsTax {

    /**
     * @var string
     */
    protected $base_option = 'tag_base';

    /**
     * @var string
     */
    protected $base_defined = 'tag';

    /**
     * Check and correct URL
     * 
     * @param string $url
     * @return string
     */
    protected function check_url( $url ) {
        if ( empty( $url ) || !is_string( $url ) )
            return '';
        $base = $this->get_base();
        if ( $this->base != $base ) {
            $search  = '/' . $this->base . '/';
            $replace = '/' . $base . '/';
            $count   = 1;
            $url     = str_replace( $search, $replace, $url, $count );
        }
        return $url;
    }

    /**
     * Get base
     * 
     * @return string
     */
    protected function get_base() {
        $base = get_option( $this->base_option );
        return( !empty( $base ) ? $base: $this->base_defined );
    }

}
