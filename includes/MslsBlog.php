<?php

/**
 * Internal representation of a blog
 *
 * @package Msls
 * @subpackage Main
 */
class MslsBlog {

    /**
     * @var StdClass WordPress generates such an object
     */
    private $obj;

    /**
     * @var string Language-code eg. de_DE
     */
    private $language;

    /**
     * @var string Description eg. Deutsch
     */
    private $description;

    /**
     * Constructor
     *
     * @param StdClass $obj 
     * @param string description
     */
    public function __construct( $obj, $description ) {
        if ( is_object( $obj ) ) {
            $this->obj      = $obj;
            $this->language = (string) get_blog_option( 
                $this->obj->userblog_id, 'WPLANG'
            );
        }
        $this->description = (string) $description;
    }

    /**
     * Get a member of the StdClass-object by name
     *
     * The method return <em>null</em> if the requested member does not exists.
     * 
     * @param string $key
     * @return mixed|null
     */
    final public function __get( $key ) {
        return( isset( $this->obj->$key ) ? $this->obj->$key : null );
    }

    /**
     * Get the description stored in this object
     * 
     * The method returns the stored language if the description is empty.
     * 
     * @return string
     */
    public function get_description() {
        return(
            !empty( $this->description ) ?
            $this->description :
            $this->get_language()
        );
    }

    /**
     * Get the language stored in this object
     * 
     * The method returns the string 'us' if there is an empty value in language.  
     * 
     * @return string
     */
    public function get_language() {
        return( !empty( $this->language ) ? $this->language : 'us' );
    }

    /**
     * Sort objects helper
     * 
     * @param mixed $a
     * @param mixed $b
     * return int
     */
    public static function _cmp( $a, $b ) {
        if ( $a == $b )
            return 0;
        return( $a < $b ? (-1) : 1 );
    }

    /**
     * Sort objects by language
     * 
     * @param mixed $a
     * @param mixed $b
     * return int
     */
    public static function language( $a, $b ) {
        return( self::_cmp( $a->get_language(), $b->get_language() ) );
    }

    /**
     * Sort objects by description
     * 
     * @param mixed $a
     * @param mixed $b
     * return int
     */
    public static function description( $a, $b ) {
        return( self::_cmp( $a->get_description(), $b->get_description() ) );
    }

}
