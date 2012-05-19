<?php

/**
 * Widget
 *
 * @package Msls
 * @subpackage Output
 */
class MslsWidget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            false,
            $name = __( 'Multisite Language Switcher', 'msls' )
        );
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
        echo $before_widget;
        $title = apply_filters( 'widget_title', $instance['title'] );
        if ( $title )
            echo $before_title . $title . $after_title;
        $obj = new MslsOutput();
        echo $obj;
        echo $after_widget;
    }

    /**
     * Update widget in the backend
     * 
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance          = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    /**
     * Display an input-form in the backend
     * 
     * @param array $instance
     */
    public function form( $instance ) {
        printf(
            '<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
            $this->get_field_id( 'title' ),
            __( 'Title', 'msls' ),
            $this->get_field_name( 'title' ),
            ( isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '' )
        );
    }

}
