<?php

/**
 * OptionsQueryAuthor
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsQueryAuthor extends MslsOptionsQuery {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) ) {
            global $wpdb;
            $sql                  = sprintf(
                "SELECT count(ID) FROM {$wpdb->posts} WHERE post_author = %d AND post_status = 'publish'",
                (int) $this->args[0]
            );
            $this->arr[$language] = $wpdb->get_var( $sql );
        }
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_author_posts_url( $this->args[0] );
    }

}
