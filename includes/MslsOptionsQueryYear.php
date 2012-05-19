<?php

/**
 * OptionsQueryYear
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsQueryYear extends MslsOptionsQuery {

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
                "SELECT count(ID) FROM {$wpdb->posts} WHERE YEAR(post_date) = %d AND post_status = 'publish'",
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
        return get_year_link( $this->args[0] );
    }

}
