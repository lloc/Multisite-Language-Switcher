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

    protected $post_type = 'post';

    static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() ) {
            $obj = new self();
            if ( isset( $_REQUEST['taxonomy'] ) ) {
                $obj->post_type = esc_attr( $_REQUEST['taxonomy'] );
                add_filter( 'manage_edit-{$obj->taxonomy}_columns' , array( $obj, 'manage' ) );
                add_action( 'manage_{$obj->taxonomy}_custom_column' , array( $obj, 'tx_columns' ), 10, 3 );
            }
            else {
                if ( isset( $_REQUEST['post_type'] ) ) 
                    $obj->post_type = esc_attr( $_REQUEST['post_type'] );
                add_filter( 'manage_{$obj->post_type}s_posts_columns' , array( $obj, 'manage' ) );
                add_action( 'manage_{$obj->post_type}s_custom_column' , array( $obj, 'pt_columns' ), 10, 2 );
            }
        }
    }

    function manage( $columns ) {
        $blogs = $this->blogs->get();
        if ( $blogs ) {
            $arr = array();
            foreach ( $blogs as $blog ) {
                $language = $blog->get_language();
                $icon     = new MslsAdminIcon( $this->post_type );
                $icon->set_language( $language );
                $icon->set_src( $this->get_flag_url( $language, true ) );
                $arr[] = $icon->get_img();
            }
            $columns['mslscol'] = implode( '&nbsp;', $arr );
        }
        return $columns;
    }

    public function tx_columns( $deprecated, $column_name, $term_id ) {
        $this->columns( $column_name, $term_id );
    }

    public function pt_columns( $column_name, $post_id ) {
        $this->columns( $column_name, $post_id );
    }

    protected function columns( $column_name, $item_id ) {
        if ( 'mslscol' == $column_name ) {
            $blogs = $this->blogs->get();
            if ( $blogs ) {
                $mydata = MslsOptionsFactory::create( $this->post_type, $item_id );
                foreach ( $blogs as $blog ) {
                    switch_to_blog( $blog->userblog_id );
                    $language  = $blog->get_language();
                    $edit_link = MslsAdminIcon::create( $this->post_type );
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

?>
