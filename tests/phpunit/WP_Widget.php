<?php

namespace lloc\MslsTests;

#[AllowDynamicProperties]
class WP_Widget {

	public $id_base;

	/**
	 * PHP5 constructor.
	 *
	 * @since 2.8.0
	 *
	 * @param string $id_base         Base ID for the widget, lowercase and unique. If left empty,
	 *                                a portion of the widget's PHP class name will be used. Has to be unique.
	 * @param string $name            Name for the widget displayed on the configuration page.
	 * @param array  $widget_options  Optional. Widget options. See wp_register_sidebar_widget() for
	 *                                information on accepted arguments. Default empty array.
	 * @param array  $control_options Optional. Widget control options. See wp_register_widget_control() for
	 *                                information on accepted arguments. Default empty array.
	 */
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
	}


	/**
	 * @param string $field_name Field name.
	 *
	 * @return string ID attribute for `$field_name`.
	 */
	public function get_field_id( string $field_name ) {
		return sprintf( 'field-id-%s', $field_name );
	}

	/**
	 * @param string $field_name Field name.
	 *
	 * @return string Name attribute for `$field_name`.
	 */
	public function get_field_name( $field_name ) {
		return sprintf( 'field-name-%s', $field_name );
	}
}
