<?php

/**
 * Options
 *
 * @package Msls
 */

require_once dirname( __FILE__ ) . '/MslsRegistry.php';

/**
 * MslsOptionsFactory
 */
class MslsOptionsFactory {

    static function create( $type = '', $id = 0 ) {
        if ( '' == $type ) {
            if ( is_category() ) {
                return new MslsCategoryOptions( get_query_var( 'cat' ) );
            } elseif ( is_tag() ) {
                return new MslsTermOptions( get_query_var( 'tag_id' ) );
            }
            global $post;
            return new MslsPostOptions( $post->ID );
        }
        else {
            $id = (int) $id;
            switch ( $type ) {
                case 'category':
                    return new MslsCategoryOptions( $id );
                    break;
                case 'post_tag':
                    return new MslsTermOptions( $id );
                    break;
                default:
                    return new MslsPostOptions( $id );
                    break;
            }
        }
        return null;
    }

}

/**
 * MslsOptions
 */
class MslsOptions extends MslsGetSet implements IMslsRegistryInstance {

    protected $args;
    protected $name;
    protected $exists   = false;
    protected $sep      = '';
    protected $autoload = 'yes';
    protected $base;

    public function __construct() {
        $this->args   = func_get_args();
        $this->name   = 'msls' . $this->sep . implode( $this->sep, $this->args );
        $this->exists = $this->set( get_option( $this->name ) );
        $this->base   = $this->get_base();
    }

    public function save( $arr ) {
        if ( $this->set( $arr ) ) {
            delete_option( $this->name );
            add_option( $this->name, $this->getArr(), '', $this->autoload );
        }
    }

    public function set( $arr ) {
        if ( is_array( $arr ) ) {
            foreach ( $arr as $key => $value ) {
                $this->__set( $key, $value );
            }
            return true;
        }
        return false;
    }

    protected function get_base() {
        return null;
    }

    public function get_permalink( $language ) {
        $postlink = $this->get_postlink( $language );
        return(
            $postlink ?
            $postlink :
            site_url()
        );
    }

    public function get_postlink( $language ) {
        return null;
    }

    public function get_current_link() {
        return site_url();
    }

    public function is_excluded() {
        return $this->has_value( 'exclude_current_blog' );
    }

    public function is_content_filter() {
        return $this->has_value( 'content_filter' );
    }

    public function get_order() {
        return ( 
            $this->has_value( 'sort_by_description' ) ?
            'description' :
            'language'
        );
    }

    public static function instance() {
        $registry = MslsRegistry::singleton();
        $cls      = __CLASS__;
        $obj      = $registry->get_object( $cls );
        if ( is_null( $obj ) ) {
            $obj = new $cls;
            $registry->set_object( $cls, $obj );
        }
        return $obj;
    }

}

/**
 * MslsPostOptions
 */
class MslsPostOptions extends MslsOptions {

    protected $sep      = '_';
    protected $autoload = 'no';

    public function get_postlink( $language ) {
        return(
            $this->has_value( $language ) ? 
            get_permalink( (int) $this->__get( $language ) ) :
            null
        );
    }

    public function get_current_link() {
        return get_permalink( (int) $this->args[0] );
    }

}

/**
 * MslsTermOptions
 */
class MslsTermOptions extends MslsOptions {

    protected $sep          = '_term_';
    protected $autoload     = 'no';
    protected $base_option  = 'tag_base';
    protected $base_defined = 'tag';
    protected $taxonomy     = 'post_tag';

    protected function get_base() {
        $base = get_option( $this->base_option );
        return(
            !empty ($base) ?
            $base :
            $this->base_defined
        );
    }

    public function get_postlink( $language ) {
        if ( $this->has_value( $language ) ) {
            $url = get_term_link(
                (int) $this->__get( $language ), 
                $this->taxonomy
            );
            if ( empty( $url ) || !is_string( $url ) )
                return null;
            $base = $this->get_base();
            if ( $this->base != $base ) {
                $search  = '/' . $this->base . '/';
                $replace = '/' . $base . '/';
                $count   = 1;
                $url     = str_replace( $search, $replace, $url, $count );
            }
            return $url;
        }
        return null;
    }

    /**
     * @return string
     */
    public function get_current_link() {
        return get_tag_link( (int) $this->args[0] );
    }

}

/**
 * MslsCategoryOptions
 */
class MslsCategoryOptions extends MslsTermOptions {

    protected $base_option  = 'category_base';
    protected $base_defined = 'category';
    protected $taxonomy     = 'category';

    /**
     * @return string
     */
    public function get_current_link() {
        return get_category_link( (int) $this->args[0] );
    }

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
