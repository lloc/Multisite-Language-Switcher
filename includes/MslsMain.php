<?php

/**
 * Main
 *
 * @package Msls
 */

/**
 * MslsContentTypes implements IMslsRegistryInstance
 */
require_once dirname( __FILE__ ) . '/MslsRegistry.php';

/**
 * MslsMain requests a instance of MslsOptions
 */
require_once dirname( __FILE__ ) . '/MslsOptions.php';

/**
 * MslsMain requests a instance of MslsBlogCollection
 */
require_once dirname( __FILE__ ) . '/MslsBlogs.php';

/**
 * Abstraction for the hook classes
 *
 * @package Msls
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
        load_plugin_textdomain(
            'msls',
            false,
            dirname( MSLS_PLUGIN_PATH ) . '/languages/'
        );
        $this->options = MslsOptions::instance();
        $this->blogs   = MslsBlogCollection::instance();
    }

    /**
     * Get type of post
     * 
     * @return string
     */
    public function get_post_type() {
        return MslsPostType::instance()->get_request();
    }

    /**
     * Get type of taxonomy
     * 
     * @return string
     */
    public function get_taxonomy() {
        return MslsTaxonomy::instance()->get_request();
    }

    /**
     * Save
     * 
     * @param integer $id
     * @param string $class
     */
    protected function save( $id, $class ) {
        if ( isset( $_POST['msls'] ) ) {
            $mydata  = $_POST['msls'];
            $options = new $class( $id );
            $options->save( $mydata );
            $language = $this->blogs->get_current_blog()->get_language();
            $mydata[$language] = $id;
            foreach ( $this->blogs->get() as $blog ) {
                $language = $blog->get_language();
                if ( !empty( $mydata[$language] ) ) {
                    switch_to_blog( $blog->userblog_id );
                    $temp    = $mydata;
                    $options = new $class( $temp[$language] );
                    unset( $temp[$language] );
                    $options->save( $temp );
                    restore_current_blog();
                }
            }
        }
    }

}

/**
 * Provides functionalities for activation an deactivation
 *
 * @package Msls
 */
class MslsPlugin {

    /**
     * Activate plugin
     */
    public static function activate() {
        if ( function_exists( 'is_multisite' ) && is_multisite() ) 
            return; 
        deactivate_plugins( __FILE__ );
        die(
            "This plugin needs the activation of the multisite-feature for working properly. Please read <a href='http://codex.wordpress.org/Create_A_Network'>this post</a> if you don't know the meaning.\n"
        );
    }

    /**
     * Deactivate plugin
     * 
     * @todo Write the deactivate-method
     */
    public static function deactivate() { }

}

/**
 * Generic class for overloading properties
 *
 * <code>
 * $obj = new MslsGetSet;
 * $obj->test = 'This is just a test';
 * echo $obj->test;
 * </code>
 * 
 * @package Msls
 */
class MslsGetSet {

    /**
     * @var array
     */
    protected $arr = array();

    /**
     * "Magic" set arg
     *
     * @param mixed $key
     * @param mixed $value
     */
    final public function __set( $key, $value ) {
        $this->arr[$key] = $value;
        if ( empty( $this->arr[$key] ) )
            unset( $this->arr[$key] );
    }

    /**
     * "Magic" get arg
     *
     * @param mixed $key
     * @return mixed
     */
    final public function __get( $key ) {
        return isset( $this->arr[$key] ) ? $this->arr[$key] : null;
    }

    /**
     * "Magic" isset
     *
     * @param mixed $key
     * @return bool
     */
    final public function __isset( $key ) {
        return isset( $this->arr[$key] );
    }

    /**
     * Check if the array has an non emty item
     * 
     * @param string $key
     * @return bool
     */ 
    final public function has_value( $key ) {
        return !empty( $this->arr[$key] ) ? true : false;
    }

    /**
     * Get args-array
     *
     * @return array
     */
    final protected function get_arr() {
        return $this->arr;
    }

}

/**
 * Supported content types
 *
 * @package Msls
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

/**
 * Supported post types
 *
 * @package Msls
 */
class MslsPostType extends MslsContentTypes implements IMslsRegistryInstance {

    public function __construct() {
        $args          = array(
            'public'   => true,
            '_builtin' => false,
        ); 
        $post_types    = get_post_types( $args, 'names', 'and' ); 
        $this->types   = array_merge( array( 'post', 'page' ), $post_types );
        $this->request = (
            isset( $_REQUEST['post_type'] ) ? 
            esc_attr( $_REQUEST['post_type'] ) :
            'post'
        );
    }

    /**
     * Check for post_type
     * 
     * @return bool
     */
    function is_post_type() {
        return true;
    }

    /**
     * Get or create a instance of MslsPostType
     *
     * @return MslsPostType
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

/**
 * Supported taxonomies
 *
 * @package Msls
 */
class MslsTaxonomy extends MslsContentTypes implements IMslsRegistryInstance {

    /**
     * Constructor
     */
    public function __construct() {
        $args        = array(
            'public'   => true,
            '_builtin' => false,
        ); 
        $request     = esc_attr( $_REQUEST['taxonomy'] );
        $taxonomies  = get_taxonomies( $args, 'names', 'and' ); 
        $this->types = array_merge( array( 'category', 'post_tag' ), $taxonomies );
    }

    /**
     * Check for taxonomy
     * 
     * @return bool
     */
    public function is_taxonomy() {
        return true;
    }

    /**
     * Get or create a instance of MslsTaxonomy
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

?>
