<?php

/**
 * OptionsPost
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsPost extends MslsOptions {

    /**
     * @var string
     */
    protected $sep = '_';

    /**
     * @var string
     */
    protected $autoload = 'no';

    /**
     * Get postlink
     * 
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        if ( $this->has_value( $language ) ) {
            $id   = (int) $this->__get( $language );
            $post = get_post( $id );
            if ( !is_null( $post ) && 'publish' == $post->post_status )
                return get_permalink( $post );
        }
        return '';
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_permalink( (int) $this->args[0] );
    }

}
