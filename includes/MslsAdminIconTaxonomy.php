<?php

/**
 * AdminIconTaxonomy
 * 
 * @package Msls
 * @subpackage Link
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
