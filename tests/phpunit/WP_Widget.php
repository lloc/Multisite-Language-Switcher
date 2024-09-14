<?php

namespace lloc\MslsTests;

class WP_Widget {


	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
	}

	public function get_field_id( $id ) {
		return $id;
	}

	public function get_field_name( $name ) {
		return $name;
	}
}
