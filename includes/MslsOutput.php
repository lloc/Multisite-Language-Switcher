<?php

/**
 * Output
 *
 * @package Msls
 */

/**
 * MslsOutput extends MslsMain and implements IMslsMain
 */
require_once dirname( __FILE__ ) . '/MslsMain.php';

/**
 * MslsOutput::get() uses MslsLink::create()
 */
require_once dirname( __FILE__ ) . '/MslsLink.php';

/**
 * Output in the frontend
 *
 * @package Msls
 */
class MslsOutput extends MslsMain implements IMslsMain {

    /**
     * Init
     * 
     * Just a placeholder
     * @access public
     */
    public static function init() { }

    /**
     * Get the output as array
     * 
     * @access public
     * @param string $display
     * @param bool $exists
     * @return array
     */
    public function get( $display, $exists = false ) {
        $arr   = array();
        $blogs = $this->blogs->get( (false == $exists ? true : false) );
        if ( $blogs ) {
            $mydata = MslsOptionsFactory::create();
            $link   = MslsLink::create( $display );
            foreach ( $blogs as $blog ) {
                $language = $blog->get_language();
                if ( true == $exists && !$mydata->has_value( $language ) )
                    continue;
                if ( $blog->userblog_id != $this->blogs->get_current_blog_id() ) {
                    switch_to_blog( $blog->userblog_id );
                    $url = $mydata->get_permalink( $language );
                    restore_current_blog();
                }
                else {
                    $url = $mydata->get_current_link();
                }
                $link->txt = $blog->get_description();
                $link->src = $this->get_flag_url( $language );
                $link->alt = $language;
                $arr[]     = sprintf(
                    '<a href="%s" title="%s">%s</a>',
                    $url,
                    $link->txt,
                    $link
                );
            }
        }
        return $arr;
    }

    /**
     * Returns a string when the object will treated like a string
     * 
     * @see get_the_msls()
     * @return string
     */ 
    public function __toString() {
        $arr = $this->get(
            (int) $this->options->display,
            (bool) $this->options->only_with_translation
        );
        $str = '';
        if ( !empty( $arr ) ) {
            $str = $this->options->before_output .
                $this->options->before_item .
                implode(
                    $this->options->after_item . $this->options->before_item,
                    $arr
                ) .
                $this->options->after_item .
                $this->options->after_output;
        }
        return $str;
    }

}

/**
 * Output in the frontend
 *
 * @package Msls
 */
class MslsWidget extends WP_Widget {

    /**
     * Constructor
     * 
     * @access public
     */
    public function __construct() {
        parent::__construct( false, $name = __( 'Multisite Language Switcher', 'msls' ) );
    }

    /**
     * Output of the widget in the frontend
     * 
     * @param array $args
     * @param array instance
     * @uses MslsOutput
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;
        $obj = new MslsOutput();
        echo $obj;
        echo $after_widget;
    }

    public function update( $new_instance, $old_instance ) {
        $instance          = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    public function form( $instance ) {
        $title = esc_attr( $instance['title'] );
        printf(
            '<p><label for="%s">%s:</label> <input class="widefat" id="%s" name="%s" type="text" value="%s" /></p>',
            $this->get_field_id( 'title' ),
            __( 'Title', 'msls' ),
            $this->get_field_id( 'title' ),
            $this->get_field_name( 'title' ),
            $title
        );
    }

}

/**
 * Registers Widget
 * 
 * @uses MslsOptions
 */
function msls_widgets_init() {
    $options = MslsOptions::instance();
    if ( !$options->is_excluded() )
        register_widget( 'MslsWidget' );
}
add_action( 'widgets_init', 'msls_widgets_init' );

/**
 * Filter for the_content()
 * 
 * @uses MslsOptions
 * @uses MslsOutput
 * @param string $content
 * @return string
 */ 
function msls_content_filter( $content ) {
    $options = MslsOptions::instance();
    if ( $options->is_content_filter() ) {
        $obj   = new MslsOutput();
        $links = $obj->get( 1, true );
        if ( !empty( $links ) ) {
            if ( count( $links ) > 1 ) {
                $last  = array_pop( $links );
                $links = sprintf(
                    __( '%s and %s', 'msls' ),
                    implode( ', ', $links ),
                    $last
                );
            } else {
                $links = $links[0];
            }
            $content .= '<p id="msls">' .
                sprintf(
                    __( 'This post is also available in %s.', 'msls' ),
                    $links
                ) .
                '</p>';
        }
    }
    return $content;
}
add_filter( 'the_content', 'msls_content_filter' );

/**
 * Get the output for using the links to the translations in your code
 * 
 * @return string
 * @see the_msls()
 */
function get_the_msls() {
    $obj = new MslsOutput();
    return( sprintf( '%s', $obj ) );
}

/**
 * Output the links to the translations in your template
 * 
 * @uses get_the_msls()
 */
function the_msls() {
    echo get_the_msls();
}

?>
