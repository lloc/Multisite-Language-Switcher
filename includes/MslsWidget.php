<?php
/**
 * MslsWidget
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

use lloc\Msls\Component\Component;

/**
 * The standard widget of the Multisite Language Switcher
 *
 * @package Msls
 */
class MslsWidget extends \WP_Widget {

	public const ID_BASE = 'mslswidget';

	public function __construct() {
		$name = apply_filters(
			'msls_widget_title',
			__( 'Multisite Language Switcher', 'multisite-language-switcher' )
		);

		parent::__construct( self::ID_BASE, $name, array( 'show_instance_in_rest' => true ) );
	}

	public static function init(): void {
		if ( ! msls_options()->is_excluded() ) {
			register_widget( self::class );
		}
	}

	/**
	 * @param array<string, mixed> $args
	 * @param array<string, mixed> $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$default = array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		);

		$args = wp_parse_args( $args, $default );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $instance['title'] ?? '', $instance, $this->id_base );
		if ( $title ) {
			$title = $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		$content = msls_output()->__toString();
		if ( '' === $content ) {
			$text    = __( 'No available translations found', 'multisite-language-switcher' );
			$content = apply_filters( 'msls_widget_alternative_content', $text );
		}

		echo wp_kses(
			$args['before_widget'] . $title . $content . $args['after_widget'],
			Component::get_allowed_html()
		);
	}

	/**
	 * Update widget in the backend
	 *
	 * @param array<string, mixed> $new_instance
	 * @param array<string, mixed> $old_instance
	 *
	 * @return array<string, mixed>
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( isset( $new_instance['title'] ) ) {
			$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		}

		return $instance;
	}

	/**
	 * Display an input-form in the backend
	 *
	 * @param array<string, mixed> $instance
	 */
	public function form( $instance ) {
		$form = sprintf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			$this->get_field_id( 'title' ),
			__( 'Title', 'multisite-language-switcher' ),
			$this->get_field_name( 'title' ),
			( isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '' )
		);

		echo wp_kses( $form, Component::get_allowed_html() );

		return $form;
	}
}
