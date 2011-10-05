<?php

/**
 * Custom Column
 *
 * @package Msls
 */

require_once dirname( __FILE__ ) . '/MslsMain.php';
require_once dirname( __FILE__ ) . '/MslsOptions.php';
require_once dirname( __FILE__ ) . '/MslsLink.php';

class MslsCustomColumn extends MslsMain implements IMslsMain {

    protected $type = 'post';

    static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() ) {
            $obj = new self();
            if ( isset( $_REQUEST['post_type'] ) ) 
                $obj->set_type ( $_REQUEST['post_type'] );
            $post_type = $obj->get_type();
            add_filter( 'manage_{$post_type}_post_columns' , array( $obj, 'th' ) );
            add_action( 'manage_{$post_type}_posts_custom_column' , array( $obj, 'td' ), 10, 2 );
        }
    }

    public function set_type( $type ) {
        $this->type = esc_attr( $type );
        return $this;
    }

    public function get_type() {
        return $this->type;
    }

    function th( $columns ) {
        $blogs = $this->blogs->get();
        if ( $blogs ) {
            $arr = array();
            foreach ( $blogs as $blog ) {
                $language = $blog->get_language();
                $icon     = new MslsAdminIcon( $this->type );
                $icon->set_language( $language );
                $icon->set_src( $this->get_flag_url( $language, true ) );
                $arr[] = $icon->get_img();
            }
            $columns['mslscol'] = implode( '&nbsp;', $arr );
        }
        return $columns;
    }

    public function td( $column_name, $post_id ) {
        $this->columns( $column_name, $post_id );
    }

    protected function columns( $column_name, $item_id ) {
        if ( 'mslscol' == $column_name ) {
            $blogs = $this->blogs->get();
            if ( $blogs ) {
                $mydata = MslsOptionsFactory::create( $this->type, $item_id );
                foreach ( $blogs as $blog ) {
                    switch_to_blog( $blog->userblog_id );
                    $language  = $blog->get_language();
                    $edit_link = MslsAdminIcon::create( $this->type );
                    $edit_link->set_language( $language );
                    if ( $mydata->has_value( $language ) ) {
                        $edit_link->set_src( $this->get_url( 'images' ) . '/link_edit.png' );
                        $edit_link->set_href( $mydata->$language );
                    }
                    else {
                        $edit_link->set_src( $this->get_url( 'images' ) . '/link_add.png' );
                    }
                    echo $edit_link;
                    restore_current_blog();
                }
            }
        }
    }

}

class MslsCustomColumnTaxonomy extends MslsCustomColumn {

    static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() ) {
            $obj = new self();
            $taxonomy = $obj->set_type( $_REQUEST['taxonomy'] )->get_type();
            add_filter( 'manage_edit-{$taxonomy}_columns' , array( $obj, 'th' ) );
            add_action( 'manage_{$taxonomy}_custom_column' , array( $obj, 'td' ), 10, 3 );
        }
    }

    public function td( $deprecated, $column_name, $term_id ) {
        $this->columns( $column_name, $term_id );
    }

}

?>
