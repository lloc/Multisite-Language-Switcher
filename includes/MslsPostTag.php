<?php

/**
 * PostTag
 * 
 * @package Msls
 * @subpackage Main
 */
class MslsPostTag extends MslsMain {

    /**
     * Init
     */
    public static function init() {
        $options = MslsOptions::instance();
        if ( !$options->is_excluded() && isset( $_REQUEST['taxonomy'] ) ) {
            $taxonomy = MslsContentTypes::create()->get_request();
            if ( !empty( $taxonomy ) ) {
                $tax = get_taxonomy( $taxonomy );
                if ( $tax && current_user_can( $tax->cap->manage_terms ) ) {
                    $obj = new self();
                    add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'add' ) );
                    add_action( "{$taxonomy}_add_form_fields", array( $obj, 'add' ) );
                    add_action( "edited_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
                    add_action( "create_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
                }
            }
        }
    }

    /**
     * Add
     * 
     * @param StdClass
     */
    public function add( $tag ) {
        $term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
        $blogs   = $this->blogs->get();
        if ( $blogs ) {
            printf(
                '<tr><th colspan="2"><strong>%s</strong></th></tr>',
                __( 'Multisite Language Switcher', 'msls' )
            );
            $mydata = MslsOptionsTax::create( $term_id );
            $type   = MslsContentTypes::create()->get_request();
            foreach ( $blogs as $blog ) {
                switch_to_blog( $blog->userblog_id );
                $language = $blog->get_language();
                $icon     = MslsAdminIcon::create();
                $options  = '';
                $terms    = get_terms( $type, array( 'hide_empty' => 0 ) );
                $icon->set_language( $language );
                $icon->set_src( $this->options->get_flag_url( $language ) );
                if ( $mydata->has_value( $language ) )
                    $icon->set_href( $mydata->$language );
                if ( !empty( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $options .= sprintf(
                            '<option value="%s"%s>%s</option>',
                            $term->term_id,
                            ( $term->term_id == $mydata->$language ? ' selected="selected"' : '' ),
                            $term->name
                        );
                    }
                }
                printf(
                    '<tr class="form-field"><th scope="row" valign="top"><label for="msls[%1$s]">%2$s </label></th><td><select class="msls-translations" name="msls[%1$s]"><option value=""></option>%3$s</select></td>',
                    $language,
                    $icon,
                    $options
                );
                restore_current_blog();
            }
        }
    }

    /**
     * Set
     * 
     * @param int $term_id
     * @param int $tt_id
     */
    public function set( $term_id, $tt_id ) {
        $arr                                                   = $_POST['msls'];
        $arr[$this->blogs->get_current_blog()->get_language()] = $term_id;
        $this->save( $term_id, 'MslsOptionsTax', $arr );
    }

}
