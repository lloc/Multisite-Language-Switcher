<?php

/**
 * Options
 * 
 * @package Msls
 * @subpackage Options
 */
class MslsOptions extends MslsGetSet implements IMslsRegistryInstance {

    /**
     * @var array
     */
    protected $args;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $exists = false;

    /**
     * @var string
     */
    protected $sep = '';

    /**
     * @var string
     */
    protected $autoload = 'yes';

    /**
     * @var string
     */
    protected $base;

    /**
     * Factory method
     * 
     * @param int $id
     * @return MslsOptions
     */
    public static function create( $id = 0 ) {
        if ( is_admin() ) {
            $id  = (int) $id;
            $obj = MslsContentTypes::create();
            if ( $obj->is_taxonomy() )
                return MslsOptionsTax::create( $id );
            return new MslsOptionsPost( $id );
        }
        else {
            if ( is_front_page() || is_search() || is_404() )
                return new MslsOptions();
            elseif ( is_category() || is_tag() || is_tax() )
                return MslsOptionsTax::create();
            elseif ( is_date() || is_author() || is_post_type_archive() )
                return MslsOptionsQuery::create();
            global $wp_query;
            return new MslsOptionsPost( $wp_query->get_queried_object_id() );
        }
        return null;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->args   = func_get_args();
        $this->name   = 'msls' . $this->sep . implode( $this->sep, $this->args );
        $this->exists = $this->set( get_option( $this->name ) );
        $this->base   = $this->get_base();
    }

    /**
     * Save
     * 
     * @param mixed $arr
     */
    public function save( $arr ) {
        $this->delete();
        if ( $this->set( $arr ) ) {
            $arr = $this->get_arr();
            if ( !empty( $arr ) )
                add_option( $this->name, $arr, '', $this->autoload );
        }
    }

    /**
     * Delete
     */
    public function delete() {
        $this->reset();
        if ( $this->exists )
            delete_option( $this->name );
    }

    /**
     * Set
     * 
     * @param mixed $arr
     * @return bool
     */
    public function set( $arr ) {
        if ( is_array( $arr ) ) {
            foreach ( $arr as $key => $value )
                $this->__set( $key, $value );
            return true;
        }
        return false;
    }

    /**
     * Get base
     * 
     * @return null
     */
    protected function get_base() {
        return null;
    }

    /**
     * Get permalink
     * 
     * @param string $language
     * @return string
     */
    public function get_permalink( $language ) {
        $postlink = $this->get_postlink( $language );
        if ( has_filter( 'msls_options_get_permalink' ) ) {
            $postlink = apply_filters(
                'msls_options_get_permalink',
                $postlink,
                $language
            );
        }
        return( '' != $postlink ? $postlink : site_url() );
    }

    /**
     * Get postlink
     * 
     * @param string $language
     * @return string
     */
    public function get_postlink( $language ) {
        return '';
    }

    /**
     * Get current link
     * 
     * @return string
     */
    public function get_current_link() {
        return site_url();
    }

    /**
     * Is excluded
     * 
     * @return bool
     */
    public function is_excluded() {
        return $this->has_value( 'exclude_current_blog' );
    }

    /**
     * Is content
     * 
     * @return bool
     */
    public function is_content_filter() {
        return $this->has_value( 'content_filter' );
    }

    /**
     * Get order
     * 
     * @return string
     */
    public function get_order() {
        return ( 
            $this->has_value( 'sort_by_description' ) ?
            'description' :
            'language'
        );
    }

    /**
     * Get url
     * 
     * @param string $dir
     * @return string
     */
    public function get_url( $dir ) {
        return esc_url( plugins_url( $dir, MSLS_PLUGIN__FILE__ ) );
    }

    /**
     * Get flag url
     * 
     * @param string $language
     * @param bool $plugin
     * @return string
     */
    public function get_flag_url( $language ) {
        $url = ( 
            !is_admin() && $this->has_value( 'image_url' ) ?
            $this->__get( 'image_url' ) :
            $this->get_url( 'flags' )
        );
        if ( 5 == strlen( $language ) )
            $language = strtolower( substr( $language, -2 ) );
        return sprintf( '%s/%s.png', $url, $language );
    }

    /**
     * Instance
     * 
     * @return MslsOptions
     */
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
