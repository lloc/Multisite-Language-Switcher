<?php

/**
 * Link
 *
 * @package Msls
 */

/**
 * MslsLink extends MslsGetSet
 */
require_once dirname( __FILE__ ) . '/MslsMain.php';

/**
 * MslsLink
 * 
 * @package Msls
 */
class MslsLink extends MslsGetSet {

    /**
     * @var array
     */
    protected $args = array();

    /**
     * @var string
     */
    protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

    /**
     * Get link types
     *
     * @return array
     */
    public static function get_types() {
        return array( 
            '0' => 'MslsLink',
            '1' => 'MslsLinkTextOnly',
            '2' => 'MslsLinkImageOnly',
            '3' => 'MslsLinkTextImage',
        );
    }

    /**
     * Get link description
     *
     * @return string
     */
    public static function get_description() {
        return __( 'Flag and description', 'msls' );
    }

    /**
     * Get array with all link descriptions
     *
     * @return array
     */
    public static function get_types_description() {
        $temp = array();
        foreach ( self::get_types() as $key => $class ) {
            $temp[$key] = call_user_func(
                array( $class, 'get_description' )
            );
        }
        return $temp;
    }
    
    /**
     * Factory: Create a specific instance of MslsLink
     *
     * @param int $display
     * @return MslsLink
     */
    public static function create( $display ) {
        $types = self::get_types();
        if ( !in_array( $display, array_keys( $types ), true ) ) $display = 0;
        return new $types[$display];
    }

    /**
     * Handles the request to print the object
     */
    public function __toString() {
        $temp = array();
        foreach ( array_keys( $this->get_arr() ) as $key ) {
            $temp[] = '{' . $key . '}';
        }
        return str_replace(
            $temp,
            $this->get_arr(),
            $this->format_string
        );
    }

}

/**
 * MslsLinkTextOnly
 * 
 * @package Msls
 */
class MslsLinkTextOnly extends MslsLink {

    /**
     * @var string
     */
    protected $format_string = '{txt}';

    /**
     * Get the description
     * 
     * @return string
     */
    public static function get_description() {
        return __( 'Description only', 'msls' );
    }

}

/**
 * MslsLinkImageOnly
 * 
 * @package Msls
 */
class MslsLinkImageOnly extends MslsLink {

    /**
     * @var string
     */
    protected $format_string = '<img src="{src}" alt="{alt}"/>';

    /**
     * Get the description
     * 
     * @return string
     */
    static function get_description() {
        return __( 'Flag only', 'msls' );
    }

}

/**
 * MslsLinkTextImage
 * 
 * @package Msls
 */
class MslsLinkTextImage extends MslsLink {

    /**
     * @var string
     */
    protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

    /**
     * Get the description
     * 
     * @return string
     */
    static function get_description() {
        return __( 'Description and flag', 'msls' );
    }

}

/**
 * MslsAdminIcon
 * 
 * @package Msls
 */
class MslsAdminIcon {

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $src;

    /**
     * @var string
     */
    protected $href;

    /**
     * @var int
     */
    protected $blog_id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $path = 'post-new.php';

    /**
     * Factory method
     * 
     * @return MslsAdminIcon
     */
    public static function create() {
        $obj  = MslsContentTypes::create();
        $type = $obj->get_request();
        if ( $obj->is_taxonomy() ) {
            return new MslsAdminIconTaxonomy( $type );
        }
        else {
            return new MslsAdminIcon( $type );
        }
    }

    /**
     * Constructor
     * 
     * @param string $type
     */
    public function __construct( $type ) {
        $this->type = esc_attr( $type );
        $this->set_path();
    }

    /**
     * Set the path by type
     */
    protected function set_path() {
        if ( 'post' != $this->type ) {
            $args       = array( 'post_type' => $this->type );
            $this->path = add_query_arg( $args, $this->path );
        }
    }

    /**
     * Set language
     * 
     * @param string $language
     */
    public function set_language( $language ) {
        $this->language = $language;
    }

    /**
     * Set src
     * 
     * @param string $src
     */
    public function set_src( $src ) {
        $this->src = $src;
    }

    /**
     * Set href
     * 
     * @param int $id
     */
    public function set_href( $id ) {
        $this->href = get_edit_post_link( $id );
    }

    /**
     * Handles the output when object is treated like a string
     * 
     * @return string
     */
    public function __toString() {
        return $this->get_a();
    }

    /**
     * Get image as html-tag
     * 
     * @return string
     */
    public function get_img() {
        return sprintf(
            '<img alt="%s" src="%s" />',
            $this->language,
            $this->src
        );
    }

    /**
     * Get link as html-tag
     * 
     * @return string
     */
    protected function get_a() {
        if ( !empty( $this->href ) ) {
            $href  = $this->href;
            $title = sprintf(
                __( 'Edit the translation in the %s-blog' ),
                $this->language
            );
        }
        else {
            $href  = $this->get_edit_new();
            $title = sprintf(
                __( 'Create a new translation in the %s-blog' ),
                $this->language
            );
        }
        return sprintf(
            '<a title="%s" href="%s">%s</a>&nbsp;',
            $title,
            $href,
            $this->get_img()
        );
    }

    /**
     * Get create new link
     * 
     * @return string
     */
    protected function get_edit_new() {
        return get_admin_url( get_current_blog_id(), $this->path );
    }

}

/**
 * MslsAdminIconTaxonomy
 * 
 * @package Msls
 */
class MslsAdminIconTaxonomy extends MslsAdminIcon {

    /**
     * @var string
     */
    protected $path = 'edit-tags.php';

    /**
     * Set href
     * 
     * @param int $id
     */
    public function set_href( $id ) {
        $this->href = get_edit_term_link(
            $id,
            $this->type,
            MslsTaxonomy::instance()->get_post_type()
        );
    }

    /**
     * Set the path by type
     */
    protected function set_path() {
        $args      = array( 'taxonomy' => $this->type );
        $post_type = MslsTaxonomy::instance()->get_post_type();
        if ( !empty( $post_type ) )
            $args['post_type'] = $post_type;
        $this->path = add_query_arg( $args, $this->path );
    }

}

?>
