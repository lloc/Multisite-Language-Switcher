<?php
/**
 * MslsetaBox
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Meta box for the edit mode of the (custom) post types 
 * @package Msls
 * @subpackage Main
 */
class MslsMetaBox extends MslsMain {

    static function suggest() {
        switch_to_blog( (int) $_REQUEST['blog_id'] );
        $my_query = new WP_Query(
            array(
                'post_type' => $_REQUEST['post_type'],
                'post_status' => 'any',
                'orderby' => 'title',
                'order' => 'ASC',
                'posts_per_page' => 10,
                's' => $_REQUEST['term'],
            )
        );
        $result = array();
        while ( $my_query->have_posts() ) {
            $my_query->the_post();
            $result[] = array(
                'value' => get_the_ID(),
                'label' => get_the_title()
            );
        }
        echo json_encode( $result );
        restore_current_blog();
        die();
    }

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
        $pt_arr = MslsPostType::instance()->get();
        foreach ( $pt_arr as $post_type ) {
            add_meta_box(
                'msls',
                __( 'Multisite Language Switcher', 'msls' ),
                array( $this, 'render' ),
                $post_type,
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
            $items  = '';
            wp_nonce_field( MSLS_PLUGIN_PATH, 'msls_noncename' );
            foreach ( $blogs as $blog ) {
                switch_to_blog( $blog->userblog_id );
                $language = $blog->get_language();
                $icon     = MslsAdminIcon::create();
                $icon->set_language( $language );
                $icon->set_src( $this->options->get_flag_url( $language ) );
                $value = '';
                if ( $mydata->has_value( $language ) ) {
                    $value = $mydata->$language . '|' . get_the_title( $mydata->$language );
                    $icon->set_href( $mydata->$language );
                }
                $items .= sprintf(
                    '<li><label for="msls_title_%3$s">%2$s</label><input type="hidden" id="msls_id_%3$s" name="msls_input_%1$s" value="%4$s"/><input class="msls_title" id="msls_title_%3$s" name="msls_title_%3$s" value="%5$s"/></li>',
                    $language,
                    $icon,
                    $blog->userblog_id,
                    $value,
                    get_the_title( $value )
                );
                restore_current_blog();
            }
            printf(
                '<ul>%s</ul><input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/><input type="hidden" name="msls_post_id" id="msls_post_id" value="%s"/><input type="hidden" name="msls_action" id="msls_action" value="suggest_posts"/><input type="submit" class="button-secondary" value="%s"/>',
                $items,
                $type,
                $post->ID,
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
     * @param int $post_id
     */
    public function set( $post_id ) {
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
            wp_is_post_revision( $post_id ) ||
            !isset( $_POST['msls_noncename'] ) || 
            !wp_verify_nonce( $_POST['msls_noncename'], MSLS_PLUGIN_PATH ) )
            return;
        if ( 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page' ) )
                return;
        }
        else {
            if ( !current_user_can( 'edit_post' ) )
                return;
        }
        $arr = array();
        foreach( $_POST as $key => $value ) {
            if ( false !== strpos( $key, 'msls_input_' ) && !empty( $value ) ) {
                $arr[substr( $key, 11 )] = (int) $value;
            }
        }
        $arr[$this->blogs->get_current_blog()->get_language()] = $post_id;
        error_log( print_r( $arr, true ) );
        $this->save( $post_id, 'MslsOptionsPost', $arr );
    }

}
