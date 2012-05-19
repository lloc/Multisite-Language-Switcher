<?php

/**
 * AdminIcon
 * 
 * @package Msls
 * @subpackage Link
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
        if ( $obj->is_taxonomy() )
            return new MslsAdminIconTaxonomy( $type );
        return new MslsAdminIcon( $type );
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
        if ( 'post' != $this->type )
            $this->path = add_query_arg( 
                array( 'post_type' => $this->type ),
                $this->path
            );
    }

    /**
     * Set language
     * 
     * @param string $str language
     */
    public function set_language( $str ) {
        $this->language = $str;
    }

    /**
     * Set src
     * 
     * @param string $str src
     */
    public function set_src( $str ) {
        $this->src = $str;
    }

    /**
     * Set href
     * 
     * @param int $id
     */
    public function set_href( $id ) {
        $this->href = get_edit_post_link( (int) $id );
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
                __( 'Edit the translation in the %s-blog', 'msls' ),
                $this->language
            );
        }
        else {
            $href  = $this->get_edit_new();
            $title = sprintf(
                __( 'Create a new translation in the %s-blog', 'msls' ),
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
