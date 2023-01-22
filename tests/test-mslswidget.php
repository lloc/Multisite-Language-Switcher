<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsWidget;
use Mockery;

class WP_Test_MslsWidget extends Msls_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$arr = [
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		];

		Functions\stubs( [
			'wp_parse_args'       => $arr,
			'get_option'          => [],
			'get_current_blog_id' => 1,
			'get_blogs_of_user'   => [],
			'get_users'           => [],
		] );
	}

	public function get_sut() {
		Mockery::mock( '\WP_Widget' );

		$widget = Mockery::mock( MslsWidget::class )->makePartial();
		$widget->shouldReceive( 'get_field_name' )->andReturn( 'test_field_name' );
		$widget->shouldReceive( 'get_field_id' )->andReturn( 'test_field_id' );

		return $widget;
	}

	function test_widget(): void {
		$expected = '<div><h3>Test</h3>No available translations found</div>';

		$this->expectOutputString( $expected );

		$this->get_sut()->widget( [], [ 'title' => 'Test' ] );
	}

	function test_update_empty_empty(): void {
		$result = $this->get_sut()->update( [], [] );

		$this->assertEquals( [], $result );
	}

	function test_update_string_empty(): void {
		$result = $this->get_sut()->update( [ 'title' => 'Test' ], [] );

		$this->assertEquals( [ 'title' => 'Test' ], $result );
	}

	function test_update_string_string(): void {
		$result = $this->get_sut()->update( [ 'title' => 'xyz' ], [ 'title' => 'Test' ] );

		$this->assertEquals( [ 'title' => 'xyz' ], $result );
	}

	function test_form(): void {
		$expected = '<p><label for="test_field_id">Title:</label> <input class="widefat" id="test_field_id" name="test_field_name" type="text" value="" /></p>';

		$this->expectOutputString( $expected );

		$this->assertEquals( 'mslsform', $this->get_sut()->form( [] ) );
	}

}
