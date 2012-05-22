<?php

/**
 * Meta Box
 *
 * @package Msls
 * @subpackage Main
 */
class MslsMetaBox extends MslsMain {

    /**
     * Init
     */
    static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() ) {
            $obj = new self();
            add_action( 'add_meta_boxes', array( $obj, 'add' ) );
            add_action( 'save_post', array( $obj, 'set' ) );
            add_action( 'trashed_post', array( $obj, 'delete' ) );
        }
    }

    /**
     * Add
     */
    public function add() {
        $obj = MslsPostType::instance();
        foreach ( $obj->get() as $pt ) {
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

    /**
     * Render
     */
    public function render() {
        $blogs = $this->blogs->get();
        if ( $blogs ) {
            global $post;
            $type   = get_post_type( $post->ID );
            $mydata = new MslsOptionsPost( $post->ID );
            $temp   = $post;
            $lis    = '';
            wp_nonce_field( MSLS_PLUGIN_PATH, 'msls_noncename' );
            foreach ( $blogs as $blog ) {
                switch_to_blog( $blog->userblog_id );
                $language = $blog->get_language();
                $icon     = MslsAdminIcon::create();
                $selects  = '';
                $pto      = get_post_type_object( $type );
                $icon->set_language( $language );
                $icon->set_src( $this->options->get_flag_url( $language ) );
                if ( $mydata->has_value( $language ) )
                    $icon->set_href( $mydata->$language );
                if ( $pto->hierarchical ) {
                    $selects .= wp_dropdown_pages(
                        array(
                            'post_type' => $type,
                            'selected' => $mydata->$language,
                            'name' => 'msls[' . $language . ']',
                            'show_option_none' => ' ', 
                            'sort_column' => 'menu_order, post_title',
                            'echo' => 0,
                        )
                    );
                }
                else {
                    $options  = '';
                    $my_query = new WP_Query(
                        array(
                            'post_type' => $type,
                            'post_status' => 'any',
                            'orderby' => 'title',
                            'order' => 'ASC',
                            'posts_per_page' => (-1),
                        )
                    );
                    while ( $my_query->have_posts() ) {
                        $my_query->the_post();
                        $my_id    = get_the_ID();
                        $options .= sprintf(
                            '<option value="%s"%s>%s</option>',
                            $my_id,
                            ( $mydata->$language == $my_id ? ' selected="selected"' : '' ),
                            get_the_title()
                        );
                    }
                    $selects .= sprintf(
                        '<select name="msls[%s]"><option value=""></option>%s</select>',
                        $language,
                        $options
                    );
                }
                $lis .= sprintf(
                    '<li><label for="msls[%s]">%s</label>%s</li>',
                    $language,
                    $icon,
                    $selects
                );
                restore_current_blog();
            }
            printf(
                '<ul>%s</ul><input type="submit" class="button-secondary" value="%s"/>',
                $lis,
                __( 'Update', 'msls' )
            );
            $post = $temp;
        }
        else {
            printf(
                '<p>%s</p>',
                __( 'You should define at least another blog in a different language in order to have some benefit from this plugin!', 'msls' )
            );
        }
    }

    /**
     * Set
     * 
     * @param int $post_id
     */
    public function set( $post_id ) {
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
            wp_is_post_revision( $post_id ) ||
            !isset( $_POST['msls_noncename'] ) || 
            !wp_verify_nonce( $_POST['msls_noncename'], MSLS_PLUGIN_PATH ) )
            return;
        if ( 'page' == $_POST['post_type'] )
            if ( !current_user_can( 'edit_page' ) )
                return;
        else
            if ( !current_user_can( 'edit_post' ) )
                return;
        $arr = $_POST['msls'];
        $arr[$this->blogs->get_current_blog()->get_language()] = $post_id;
        $this->save( $post_id, 'MslsOptionsPost', $arr );
    }

}
