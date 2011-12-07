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
        $this->options = MslsOptions::instance();
        $this->blogs   = MslsBlogCollection::instance();
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
            "This plugin needs the activation of the multisite-feature for working properly. Please read <a onclick='window.open(this.href); return false;' href='http://codex.wordpress.org/Create_A_Network'>this post</a> if you don't know the meaning.\n"
        );
    }

    /**
     * Deactivate plugin
     * 
     * @todo Write the deactivate-method
     */
    public static function deactivate() { }

    /**
     * Uninstall plugin
     * 
     * @todo Write the uninstall-method
     */
    public static function uninstall() { }

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
    public function has_value( $key ) {
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

/**
 * Supported post types
 *
 * @package Msls
 */
class MslsPostType extends MslsContentTypes implements IMslsRegistryInstance {

    /**
     * Constructor
     */
    public function __construct() {
        $args = array(
            'public'   => true,
            '_builtin' => false,
        ); 
        $this->types = array_merge(
            array( 'post', 'page' ),
            get_post_types( $args, 'names', 'and' )
        );
        if ( !empty( $_REQUEST['post_type'] ) ) {
            $this->request = esc_attr( $_REQUEST['post_type'] );
        }
        else {
            $this->request = get_post_type();
            if ( !$this->request ) $this->request = 'post'; 
        }
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
     * @var string
     */
    protected $post_type = '';

    /**
     * Constructor
     */
    public function __construct() {
        $args = array(
            'public'   => true,
            '_builtin' => false,
        ); 
        $this->types   = array_merge(
            array( 'category', 'post_tag' ),
            get_taxonomies( $args, 'names', 'and' )
        );
        $this->request = esc_attr( $_REQUEST['taxonomy'] );
        if ( empty( $this->request ) )
            $this->request = get_query_var( 'taxonomy' );
        if ( !empty( $_REQUEST['post_type'] ) ) {
            $this->post_type = esc_attr( $_REQUEST['post_type'] );
        }
    }

    /**
     * Get the requested post_type of the taxonomy
     * 
     * @return string
     */
    public function get_post_type() {
        return $this->post_type;
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
