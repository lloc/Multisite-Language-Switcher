<?php

/**
 * Abstraction for the hook classes
 *
 * @package Msls
 * @subpackage Main
 */
abstract class MslsMain {

    /**
     * @var MslsOptions
     */
    protected $options;

    /**
     * @var MslsBlogCollection
     */
    protected $blogs;

    /**
     * Every child of MslsMain has to define a init-method
     */
    abstract public static function init();

    /**
     * Constructor
     */
    public function __construct() {
        $this->options = MslsOptions::instance();
        $this->blogs   = MslsBlogCollection::instance();
    }

    /**
     * Save
     * 
     * @param int $post_id
     * @param string $class
     * @param array $input
     */
    protected function save( $post_id, $class, array $input ) {
        $msla     = new MslsLanguageArray( $input );
        $language = $this->blogs->get_current_blog()->get_language();
        $msla->set( $language, $post_id );
        $options  = new $class( $post_id );
        $input    = $msla->filter( $language );
        if ( !empty( $input ) )
            $options->save( $input );
        else
            $options->delete();
        foreach ( $this->blogs->get() as $blog ) {
            switch_to_blog( $blog->userblog_id );
            $language = $blog->get_language();
            $options  = new $class( $temp->$language );
            if ( !in_array( $language, $msla->languages() ) ) {
                $options->delete();
            }
            else {
                $options->save( $msla->filter( $language ) );
            }
            restore_current_blog();
        }
    }

    /**
     * Delete the connections of a post
     * 
     * @param int $post_id
     */
    public static function delete( $post_id ) {
        $options = new MslsOptionsPost( $post_id );
        $blogs   = MslsBlogCollection::instance();
        $slang   = $blogs->get_current_blog()->get_language();
        foreach ( $blogs->get() as $blog ) {
            switch_to_blog( $blog->userblog_id );
            $tlang = $blog->get_language();
            $temp  = new MslsOptionsPost( $options->$tlang );
            unset( $temp->$slang );
            if ( $temp->is_empty() )
                $temp->delete();
            else
                $temp->save( $tmp );
            restore_current_blog();
        }
        $options->delete();
    }

}
