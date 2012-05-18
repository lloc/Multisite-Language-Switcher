<?php

/**
 * Supported content types
 *
 * @package Msls
 * @subpackage Main
 */
class MslsContentTypes {

    /**
     * @var string
     */
    protected $request;

    /**
     * @var array
     */
    protected $types = array();

    /**
     * Factory method
     * 
     * @return MslsContentTypes
     */
    public static function create() {
        if ( isset( $_REQUEST['taxonomy'] ) )
            return MslsTaxonomy::instance();
        return MslsPostType::instance();
    }

    /**
     * Getter
     * 
     * @return array
     */
    public function get() {
        return $this->types;
    }

    /**
     * Gets the request if it is an allowed content type
     * 
     * @return string
     */
    public function get_request() {
        return(
            in_array( $this->request, $this->types ) ?
            $this->request :
            ''
        );
    }

    /**
     * Get the requested taxonomy without a check
     * 
     * @return string
     */
    public function get_taxonomy() {
        return $this->taxonomy;
    }
    
    /**
     * Check for post_type
     * 
     * @return bool
     */
    public function is_post_type() {
        return false;
    }

    /**
     * Check for taxonomy
     * 
     * @return bool
     */
    public function is_taxonomy() {
        return false;
    }

}
