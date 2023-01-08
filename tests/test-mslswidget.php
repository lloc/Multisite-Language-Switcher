<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsWidget;
use Mockery;

class WP_Test_MslsWidget extends Msls_UnitTestCase {

	public function get_sut() {
		Mockery::mock( '\WP_Widget' );

		return Mockery::mock( MslsWidget::class )->makePartial();
	}

	function test_widget_method() {
		$arr = [
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		];

		Functions\expect( 'wp_parse_args' )->once()->andReturn( $arr );
		Functions\expect( 'get_option' )->andReturn( [] );
		Functions\expect( 'get_current_blog_id' )->andReturn( 1 );
		Functions\expect( 'get_blogs_of_user' )->andReturn( [] );
		Functions\expect( 'get_users' )->andReturn( [] );

		$obj = $this->get_sut();

		$this->expectOutputString( 'No available translations found' );
		$obj->widget( [], [] );
	}

	function test_update_method() {
		$obj = $this->get_sut();

		$result = $obj->update( [], [] );
		$this->assertEquals( [], $result );

		$result = $obj->update( [ 'title' => 'abc' ], [] );
		$this->assertEquals( [ 'title' => 'abc' ], $result );

		$result = $obj->update( [ 'title' => 'xyz' ], [ 'title' => 'abc' ] );
		$this->assertEquals( [ 'title' => 'xyz' ], $result );
	}

}
