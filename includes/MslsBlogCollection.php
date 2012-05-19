<?php

/**
 * Collection of blog-objects
 * 
 * Implements the interface IMslsRegistryInstance because we want to work with 
 * a singleton instance of MslsBlogCollection all the time.
 * @package Msls
 * @subpackage Main
 */
class MslsBlogCollection implements IMslsRegistryInstance {

    /**
     * @var int ID of the current blog
     */
    private $current_blog_id;

    /**
     * @var bool True if the current blog should be in the output
     */
    private $current_blog_output;

    /**
     * @var array Collection of MslsBlog-objects
     */
    private $objects = array();

    /**
     * @var string Order output by language or description
     */
    private $objects_order;

    /**
     * @var array Active plugins in the whole network
     */
    private $active_plugins;

    /**
     * Constructor
     */
    public function __construct() {
        $options                   = MslsOptions::instance();
        $this->current_blog_id     = get_current_blog_id();
        $this->current_blog_output = $options->has_value( 'output_current_blog' );
        $this->objects_order       = $options->get_order();
        if ( !$options->is_excluded() ) {
            if ( has_filter( 'msls_blog_collection_construct' ) ) {
                $blogs_collection = apply_filters(
                    'msls_blog_collection_construct',
                    array()
                );
            }
            else {
                $args = array(
                    'blog_id' => $this->current_blog_id,
                    'orderby' => 'registered',
                    'fields' => 'ID',
                );
                $blogs_collection = array();
                $blog_users       = get_users( $args );
                if ( !empty ( $blog_users ) )
                    $blogs_collection = get_blogs_of_user( $blog_users[0] );
            }
            foreach ( (array) $blogs_collection as $blog ) {
                /*
                 * get_user_id_from_string returns objects with userblog_id-members 
                 * instead of a blog_id ... so we need just some correction ;)
                 *
                 */
                if ( !isset( $blog->userblog_id ) && isset( $blog->blog_id) )
                    $blog->userblog_id = $blog->blog_id;
                if ( $blog->userblog_id != $this->current_blog_id ) {
                    $temp = get_blog_option( $blog->userblog_id, 'msls' );
                    if ( is_array( $temp ) && empty( $temp['exclude_current_blog'] ) && $this->is_plugin_active( $blog->userblog_id ) )
                        $this->objects[$blog->userblog_id] = new MslsBlog(
                            $blog,
                            $temp['description']
                        );
                }
                else {
                    $this->objects[$this->current_blog_id] = new MslsBlog(
                        $blog,
                        $options->description
                    );
                }
            }
            uasort( $this->objects, array( 'MslsBlog', $this->objects_order ) );
        }
    }

    /**
     * Get the id of the current blog
     *
     * @return int ID of the current blog
     */
    public function get_current_blog_id() {
        return $this->current_blog_id;
    }

    /**
     * Check if current blog is in the collection
     *
     * @return bool Is the current blog part of the output? 
     */
    public function has_current_blog() {
        return( isset( $this->objects[$this->current_blog_id] ) );
    }

    /**
     * Get current blog as object
     *
     * @return MslsBlog|null Current blog as MslsBlog-Object
     */
    public function get_current_blog() {
        return(
            $this->has_current_blog() ?
            $this->objects[$this->current_blog_id] :
            null
        );
    }

    /**
     * Get an array with all blog-objects
     *
     * @return array Collection of MslsBlog-objects
     */
    public function get_objects() {
        return $this->objects;
    }

    /**
     * Is plugin active in blog x
     * 
     * @param int $blog_id
     * @return bool
     */
    function is_plugin_active( $blog_id ) {
        if ( !is_array( $this->active_plugins ) )
            $this->active_plugins = get_site_option( 'active_sitewide_plugins', array() );
        if ( isset( $this->active_plugins[MSLS_PLUGIN_PATH] ) )
            return true;
        $plugins = get_blog_option( $blog_id, 'active_plugins', array() );
        return( in_array( MSLS_PLUGIN_PATH, $plugins ) );
	}

    /**
     * Get an arry of blog-objects without the current blog
     * 
     * @return array Collection of MslsBlog-objects
     */
    public function get() {
        $objects = $this->get_objects();
        if ( $this->has_current_blog() )
            unset( $objects[$this->current_blog_id] );
        return $objects;
    }

    /**
     * Get an array with filtered blog-objects
     *
     * @param bool $filter
     * @return array Collection of MslsBlog-objects
     */
    public function get_filtered( $filter = false ) {
        if ( !$filter && $this->current_blog_output )  
            return $this->get_objects();
        return $this->get();
    }

    /**
     * Get or create a instance of MslsBlogCollection
     *
     * @return MslsBlogCollection
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
