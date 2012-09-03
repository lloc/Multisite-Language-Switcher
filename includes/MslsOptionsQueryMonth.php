<?php

/**
 * OptionsQueryMonth
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsQueryMonth extends MslsOptionsQuery {

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
                $wpdb->prepare(
                    "SELECT count(ID) FROM {$wpdb->posts} WHERE YEAR(post_date) = %d AND MONTH(post_date) = %d AND post_status = 'publish'",
                    $this->args[0],
                    $this->args[1]
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
        return get_month_link( $this->args[0], $this->args[1] );
    }

}
