<?php

/**
 * OptionsQueryPostType
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsQueryPostType extends MslsOptionsQuery {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) )
            $this->arr[$language] = get_post_type_object( $this->args[0] );
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_post_type_archive_link( $this->args[0] );
    }

}
