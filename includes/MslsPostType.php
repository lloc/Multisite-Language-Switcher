<?php

/**
 * PostType
 *
 * @package Msls
 * @subpackage Main
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
            if ( !$this->request )
                $this->request = 'post'; 
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
