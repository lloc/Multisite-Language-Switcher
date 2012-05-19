<?php

/**
 * Taxonomy
 *
 * @package Msls
 * @subpackage Main
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
        $this->types   = array_merge(
            array( 'category', 'post_tag' ),
            get_taxonomies(
                array( 'public'   => true, '_builtin' => false ),
                'names',
                'and'
            )
        );
        $this->request = esc_attr( $_REQUEST['taxonomy'] );
        if ( empty( $this->request ) )
            $this->request = get_query_var( 'taxonomy' );
        if ( !empty( $_REQUEST['post_type'] ) )
            $this->post_type = esc_attr( $_REQUEST['post_type'] );
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
