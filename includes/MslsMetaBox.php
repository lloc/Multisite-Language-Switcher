<?php

/**
 * Meta Box
 *
 * @package Msls
 */

require_once dirname( __FILE__ ) . '/MslsMain.php';
require_once dirname( __FILE__ ) . '/MslsLink.php';

class MslsMetaBox extends MslsMain implements IMslsMain {

    static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() ) {
            $obj = new self();
            add_action( 'add_meta_boxes', array( $obj, 'add' ) );
            add_action( 'save_post', array( $obj, 'set' ) );
        }
    }

    public function add() {
        $args = array(
            'public'   => true,
            '_builtin' => false,
        ); 
        $post_types = get_post_types( $args, 'names', 'and' ); 
        $post_types = array_merge( array( 'post', 'page' ), $post_types );
        foreach ( $post_types as $pt ) {
            add_meta_box(
                'msls',
                __( 'Multisite Language Switcher', 'msls' ),
                array( $this, 'render' ),
                $pt,
                'side',
                'high'
            );
        }
    }

    public function render() {
        global $post;
        $blogs = $this->blogs->get();
        if ( $blogs ) {
            $temp   = $post;
            $type   = get_post_type( $post );
            $mydata = new MslsPostOptions( $post->ID );
            wp_nonce_field( MSLS_PLUGIN_PATH, 'msls' . '_noncename' );
            echo '<ul>';
            foreach ( $blogs as $blog ) {
                switch_to_blog( $blog->userblog_id );
                $args = array(
                    'post_type' => $type,
                    'post_status' => 'publish',
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'posts_per_page' => (-1),
                );
                $my_query  = new WP_Query( $args );
                $language  = $blog->get_language();
                $options   = '';
                $edit_link = MslsAdminIcon::create( $type );
                $edit_link->set_language( $language );
                $edit_link->set_src( $this->get_flag_url( $language ) );
                while ( $my_query->have_posts() ) {
                    $my_query->the_post();
                    $my_id    = get_the_ID();
                    $selected = '';
                    if ( $my_id == $mydata->$language ) {
                        $selected = 'selected="selected"';
                        $edit_link->set_href( get_edit_post_link( $mydata->$language ) );
                    }
                    $options .= sprintf(
                        '<option value="%s"%s>%s</option>',
                        $my_id,
                        $selected,
                        get_the_title()
                    );
                }
                printf(
                    '<li><label for="%s[%s]">%s </label><select style="width:90%%" name="%s[%s]" class="postform"><option value=""></option>%s</select></li>',
                    'msls',
                    $language,
                    $edit_link,
                    'msls',
                    $language,
                    $options
                );
                restore_current_blog();
            }
            printf(
                '</ul><input style="align:right" type="submit" class="button-secondary" value="%s"/>',
                __( 'Update', 'msls' )
            );
            $post = $temp;
        } else {
            printf(
                '<p>%s</p>',
                __( 'You should define at least another blog in a different language in order to have some benefit from this plugin!', 'msls' )
            );
        }
    }

    public function set( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;
        if ( !isset( $_POST['msls' . '_noncename'] ) || !wp_verify_nonce( $_POST['msls' . '_noncename'], MSLS_PLUGIN_PATH ) )
            return;
        if ( 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page' ) ) return;
        } else {
            if ( !current_user_can( 'edit_post' ) ) return;
        }
        $this->save( $post_id, 'MslsPostOptions' );
    }

}

?>
