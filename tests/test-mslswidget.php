<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsWidget;

class WP_Test_MslsWidget extends Msls_UnitTestCase {

	public function get_sut() {
		\Mockery::mock( '\WP_Widget' );

		return \Mockery::mock( MslsWidget::class )->makePartial();
	}

	function test_widget_method_empty() {
		$arr = [ 'before_widget' => '', 'after_widget'  => '', 'before_title'  => '', 'after_title'   => '' ];

		Functions\expect( 'wp_parse_args' )->once()->andReturn( $arr );

		$obj = $this->get_sut();

		$this->expectOutputString( 'No available translations found' );
		$obj->widget( [], [] );
	}

	function test_widget_method_instance() {
		$args = [ 'before_widget' => '<p>', 'after_widget'  => '</p>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' ];

		Functions\expect( 'wp_parse_args' )->once()->andReturn( $args );

		$obj = $this->get_sut();

		$this->expectOutputString( '<p><h2>Test Title</h2>No available translations found</p>' );
		$obj->widget( $args, [ 'title' => 'Test Title' ] );
	}

	function test_update() {
		$obj = $this->get_sut();

		$result = $obj->update( [], [] );
		$this->assertEquals( [], $result );

		$result = $obj->update( [ 'title' => 'abc' ], [] );
		$this->assertEquals( [ 'title' => 'abc' ], $result );

		$result = $obj->update( [ 'title' => 'xyz' ], [ 'title' => 'abc' ] );
		$this->assertEquals( [ 'title' => 'xyz' ], $result );
	}

	function test_form() {
		$obj = $this->get_sut();
		$obj->shouldReceive( 'get_field_id' )->once()->andReturn( 'widget-mslswidget-1-title' );
		$obj->shouldReceive( 'get_field_name' )->once()->andReturn( 'widget-mslswidget[1][title]' );

		$title    = 'Test-Title';
		$expected = '<p><label for="widget-mslswidget-1-title">Title:</label> <input class="widefat" id="widget-mslswidget-1-title" name="widget-mslswidget[1][title]" type="text" value="' . $title . '" /></p>';
		$this->expectOutputString( $expected );
		$this->assertEquals( 'noform', $obj->form( [ 'title' => $title ] ) );
	}

}
