<?php

/**
 * OptionsQuery
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsQuery extends MslsOptions {

    /**
     * Factory method
     * 
     * @return MslsQueryOptions
     */
    public static function create() {
        if ( is_day() )
            return new MslsOptionsQueryDay( 
                get_query_var( 'year' ),
                get_query_var( 'monthnum' ),
                get_query_var( 'day' )
            );
        elseif ( is_month() )
            return new MslsOptionsQueryMonth(
                get_query_var( 'year' ),
                get_query_var( 'monthnum' )
            );
        elseif ( is_year() )
            return new MslsOptionsQueryYear(
                get_query_var( 'year' )
            );
        elseif ( is_author() )
            return new MslsOptionsQueryAuthor(
                get_queried_object_id()
            );
        elseif ( is_post_type_archive() )
            return new MslsOptionsQueryPostType(
                get_query_var( 'post_type' )
            );
        return null;
    }

    /**
     * Get postlink
     * 
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        return(
            $this->has_value( $language ) ?
            $this->get_current_link() :
            ''
        );
    }

}
