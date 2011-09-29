<?php

/**
 * Blogs
 *
 * @package Msls
 */

/**
 * MslsBlogCollection implements IMslsRegistryInstance
 */
require_once dirname( __FILE__ ) . '/MslsRegistry.php';

/**
 * MslsBlogCollection uses get_user_id_from_string()
 */
require_once ABSPATH . WPINC . '/ms-functions.php';

/**
 * Collection of blog-objects
 *
 * @package Msls
 */
class MslsBlogCollection implements IMslsRegistryInstance {

    /**
     * @access private
     * @var int
     */
    private $current_blog_id;

    /**
     * @access private
     * @var bool
     */
    private $current_blog_output;

    /**
     * @access private
     * @var array
     */
    private $objects = array();

    /**
     * @access private
     * @var string
     */
    private $objects_order;

    /**
     * Constructor
     *
     * @access public
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
                $user_id = get_user_id_from_string(
                    get_blog_option( $this->current_blog_id, 'admin_email' )
                );
                $blogs_collection = get_blogs_of_user( $user_id );
            }
            foreach ( (array) $blogs_collection as $blog ) {
                if ( $blog->userblog_id != $this->current_blog_id ) {
                    $temp = get_blog_option( $blog->userblog_id, 'msls' );
                    if ( is_array( $temp ) && empty( $temp['exclude_current_blog'] ) ) {
                        $this->objects[$blog->userblog_id] = new MslsBlog(
                            $blog,
                            $temp['description']
                        );
                    }
                }
                else {
                    $this->objects[$this->current_blog_id] = new MslsBlog(
                        $blog,
                        $options->description
                    );
                }
            }
        }
    }

    /**
     * Get the id of the current blog
     *
     * @access public
     * @return int
     */
    public function get_current_blog_id() {
        return $this->current_blog_id;
    }

    /**
     * Check if current blog is in the collection
     *
     * @access public
     * @return bool
     */
    public function has_current_blog() {
        return(
            isset( $this->objects[$this->current_blog_id] ) ?
            true :
            false
        );
    }

    /**
     * Get current blog as object
     *
     * @access public
     * @return mixed
     */
    public function get_current_blog() {
        return(
            $this->has_current_blog() ?
            $this->objects[$this->current_blog_id] :
            null
        );
    }

    /**
     * Get an array with blog-objects
     *
     * @access public
     * @param bool frontend
     * @return array
     */
    public function get( $frontend = false ) {
        $objects = apply_filters( 'msls_blog_collection_get', $this->objects );
        if ( (!$frontend || !$this->current_blog_output) && $this->has_current_blog() )
            unset( $objects[$this->current_blog_id] );
        usort( $objects, array( 'MslsBlog', $this->objects_order ) );
        return $objects;
    }

    /**
     * Get or create a instance of MslsBlogCollection
     *
     * @access public
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

/**
 * Internal representation of a blog
 *
 * @package Msls
 */
class MslsBlog {

    /**
     * @access private
     * @var StdClass
     */
    private $obj;

    /**
     * @access private
     * @var string
     */
    private $description;

    /**
     * @access private
     * @var string
     */
    private $language;

    /**
     * Constructor
     *
     * @access public
     * @param StdClass $obj
     * @param string description
     */
    public function __construct( StdClass $obj, $description ) {
        /*
         * get_user_id_from_string returns objects with userblog_id-members 
         * instead of a blog_id ... so we need just some correction ;)
         *
         */
        if ( !isset( $this->userblog_id ) ) {
            $this->userblog_id = $this->blog_id;
        }
        $this->obj         = $obj;
        $this->description = (string) $description;
        $this->language    = (string) get_blog_option( $this->obj->userblog_id, 'WPLANG' );
    }

    /**
     * Get a member of the StdClass-object by name
     *
     * The method return <em>null</em> if the requested member does not exists.
     * 
     * @access public
     * @param string $key
     * @return mixed
     */
    final public function __get( $key ) {
        return(
            isset( $this->obj->$key ) ?
            $this->obj->$key :
            null
        );
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
        return(
            !empty( $this->language ) ?
            $this->language :
            'us'
        );
    }

    /**
     * Sort objects helper
     * 
     * @param mixed $a
     * @param mixed $b
     * return int
     */
    public static function _cmp( $a, $b ) {
        if ( $a == $b ) {
            return 0;
        }
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

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
