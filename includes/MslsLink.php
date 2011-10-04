<?php

/**
 * Link
 *
 * @package Msls
 */

require_once dirname( __FILE__ ) . '/MslsMain.php';

class MslsLink extends MslsGetSet {

    /**
     * @access protected
     * @var array
     */
    protected $args = array();

    /**
     * @access protected
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
     * @access public
     * @return string
     */
    public static function get_description() {
        return __( 'Flag and description', 'msls' );
    }

    /**
     * Get array with all link descriptions
     *
     * @access public
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
     * @access public
     * @return MslsLink
     */
    public static function create( $display ) {
        $types = self::get_types();
        if ( !in_array( $display, array_keys( $types ), true ) ) $display = 0;
        return new $types[$display];
    }

    public function __toString() {
        $temp = array();
        foreach ( array_keys( $this->getArr() ) as $key ) {
            $temp[] = '{' . $key . '}';
        }
        return str_replace(
            $temp,
            $this->getArr(),
            $this->format_string
        );
    }

}

class MslsLinkTextOnly extends MslsLink {

    protected $format_string = '{txt}';

    static function get_description() {
        return __( 'Description only', 'msls' );
    }

}

class MslsLinkImageOnly extends MslsLink {

    protected $format_string = '<img src="{src}" alt="{alt}"/>';

    static function get_description() {
        return __( 'Flag only', 'msls' );
    }

}

class MslsLinkTextImage extends MslsLink {

    protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

    static function get_description() {
        return __( 'Description and flag', 'msls' );
    }

}

class MslsAdminIcon {

    protected $language;
    protected $src;
    protected $href;
    protected $blog_id;
    protected $type;

    protected $path = 'post-new.php?post_type=';

    static function create( $type ) {
        switch ( $type ) {
            case 'post':
                return new MslsAdminIconPost;
                break;
            case 'category':
            case 'post_tag':
                return new MslsAdminIconTag( $type );
                break;
        }
        return new MslsAdminIcon( $type );
    }

    public function __construct( $type ) {
        $this->type = esc_attr( $type );
        $this->path .= $this->type;
    }

    public function set_language( $language ) {
        $this->language = $language;
    }

    public function set_src( $src ) {
        $this->src = $src;
    }

    public function set_href( $id ) {
        $this->href = get_edit_post_link( $id );
    }

    public function __toString() {
        return $this->get_a();
    }

    public function get_img() {
        return sprintf(
            '<img alt="%s" src="%s" />',
            $this->language,
            $this->src
        );
    }

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

    protected function get_edit_new() {
        return get_admin_url( get_current_blog_id(), $this->path );
    }

}

class MslsAdminIconPost extends MslsAdminIcon {

    protected $path = 'post-new.php';

    public function __construct( $type ) {
        // not implemented
    }

}

class MslsAdminIconTaxonomy extends MslsAdminIcon {

    protected $path = 'edit-tags.php?taxonomy=';

    public function set_href( $id ) {
        $this->href = get_edit_term_link( $id, $this->type );
    }

}

?>
