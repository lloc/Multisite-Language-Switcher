<?php

/**
 * OptionsTax
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptionsTax extends MslsOptions {

    /**
     * @var string
     */
    protected $sep = '_term_';

    /**
     * @var string
     */
    protected $autoload = 'no';

    /**
     * Factory method
     * 
     * @param int $id
     * @return MslsOptionsTax
     */
    public static function create( $id = 0 ) {
        if ( is_admin() ) {
            $id  = (int) $id;
            $obj = MslsContentTypes::create();
            if ( $obj->is_taxonomy() ) {
                switch ( $obj->get_request() ) {
                    case 'category':
                        return new MslsOptionsTaxTermCategory( $id );
                        break;
                    case 'post_tag':
                        return new MslsOptionsTaxTerm( $id );
                        break;
                    default:
                        return new MslsOptionsTax( $id );
                }
            }
        }
        else {
            global $wp_query;
            if ( is_category() )
                return new MslsOptionsTaxTermCategory( $wp_query->get_queried_object_id() );
            elseif ( is_tag() )
                return new MslsOptionsTaxTerm( $wp_query->get_queried_object_id() );
            elseif ( is_tax() )
                return new MslsOptionsTax( $wp_query->get_queried_object_id() );
        }
        return null;
    }

    /**
     * Get the queried taxonomy
     */
    protected function get_tax_query() {
        global $wp_query;
        return(
            isset( $wp_query->tax_query->queries[0]['taxonomy'] ) ?
            $wp_query->tax_query->queries[0]['taxonomy'] :
            ''
        );
    }

    /**
     * Check and correct URL
     * 
     * @param string $url
     * @return string
     */
    protected function check_url( $url ) {
        return( empty( $url ) || !is_string( $url ) ? '' : $url );
    }

    /**
     * Get postlink
     *
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        $url = '';
        if ( $this->has_value( $language ) ) {
            $taxonomy = $this->get_tax_query();
            $url      = $this->check_url(
                get_term_link( (int) $this->__get( $language ), $taxonomy )
            );
        }
        return $url;
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        $taxonomy = $this->get_tax_query();
        return(
            !empty( $taxonomy ) ?
            get_term_link( (int) $this->args[0], $taxonomy ) :
            null
        );
    }

}
