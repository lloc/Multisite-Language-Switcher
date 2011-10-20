<?php

/**
 * Post Tag
 *
 * @package Msls
 */

/**
 * MslsPostTag extends MslsMain
 */
require_once dirname( __FILE__ ) . '/MslsMain.php';

/**
 * MslsAdminIcon is used
 */
require_once dirname( __FILE__ ) . '/MslsLink.php';

/**
 * MslsPostTag
 * 
 * @package Msls
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
                $obj = new self();
                add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'add' ) );
                add_action( "{$taxonomy}_add_form_fields", array( $obj, 'add' ) );
                add_action( "edited_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
                add_action( "create_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
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
            $mydata = MslsTaxOptions::create( $term_id );
            $type   = MslsContentTypes::create()->get_request();
            foreach ( $blogs as $blog ) {
                switch_to_blog( $blog->userblog_id );
                $language  = $blog->get_language();
                $options   = '';
                $terms     = get_terms( $type, array( 'hide_empty' => 0 ) );
                $edit_link = MslsAdminIcon::create();
                $edit_link->set_language( $language );
                $edit_link->set_src( $this->options->get_flag_url( $language ) );
                if ( !empty( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $selected = '';
                        if ( $term->term_id == $mydata->$language ) {
                            $selected = 'selected="selected"';
                            $edit_link->set_href( $mydata->$language );
                        }
                        $options .= sprintf(
                            '<option value="%s"%s>%s</option>',
                            $term->term_id,
                            $selected,
                            $term->name
                        );
                    }
                }
                printf(
                    '<tr class="form-field"><th scope="row" valign="top"><label for="%s[%s]">%s </label></th><td><select style="width:25em;" name="%s[%s]"><option value=""></option>%s</select></td>',
                    'msls',
                    $language,
                    $edit_link,
                    'msls',
                    $language,
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
     */
    public function set( $term_id, $tt_id ) {
        if ( !current_user_can( 'manage_categories' ) ) return;
        $this->save( $term_id, 'MslsTaxOptions' );
    }

}

?>
