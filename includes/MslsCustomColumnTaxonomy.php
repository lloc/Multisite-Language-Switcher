<?php

/**
 * CustomColumnTaxonomy
 * 
 * @package Msls
 * @subpackage Main
 */
class MslsCustomColumnTaxonomy extends MslsCustomColumn {

    /**
     * Init
     */
    static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() ) {
            $taxonomy = MslsTaxonomy::instance()->get_request();
            if ( !empty( $taxonomy ) ) {
                $obj = new self();
                add_filter( "manage_edit-{$taxonomy}_columns" , array( $obj, 'th' ) );
                add_action( "manage_{$taxonomy}_custom_column" , array( $obj, 'td' ), 10, 3 );
                add_action( "delete_{$taxonomy}", array( $obj, 'delete' ), 10, 2 );
            }
        }
    }

    /**
     * Table body
     * 
     * @param string $deprecated
     * @param string $column_name
     * @param int $item_id
     */
    public function td( $deprecated, $column_name, $item_id ) {
        parent::td( $column_name, $item_id );
    }

    /**
     * Delete
     * 
     * @param int $term_id
     * @param int $tt_id
     */
    public function delete( $term_id, $tt_id ) {
        $options = new MslsOptionsTax( $term_id );
        $this->save( $term_id, 'MslsOptionsTax', $options->get_arr() );
    }

}
