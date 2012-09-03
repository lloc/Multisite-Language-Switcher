<?php

/**
 * OptionsQueryDay
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsQueryDay extends MslsOptionsQuery {

    /**
     * Check if the array has an non empty item which has $language as a key
     * 
     * @param string $language
     * @return bool
     */ 
    public function has_value( $language ) {
        if ( !isset( $this->arr[$language] ) ) {
            global $wpdb;
            $this->arr[$language] = $wpdb->get_var(
                sprintf(
                    "SELECT count(ID) FROM {$wpdb->posts} WHERE DATE(post_date) = '%d-%02d-%02d' AND post_status = 'publish'",
                    (int) $this->args[0],
                    (int) $this->args[1],
                    (int) $this->args[2]
                )
            );
        }
        return (bool) $this->arr[$language];
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return get_day_link( $this->args[0], $this->args[1], $this->args[2] );
    }

}
