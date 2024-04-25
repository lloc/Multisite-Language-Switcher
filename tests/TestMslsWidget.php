<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsWidget;

class TestMslsWidget extends Msls_UnitTestCase {

	public function get_sut() {
		\Mockery::mock( '\WP_Widget' );

		return \Mockery::mock( MslsWidget::class )->makePartial();
	}

	function test_widget() {
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( [] );

		$arr = [
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		];

		Functions\expect( 'wp_parse_args' )->once()->andReturn( $arr );
		Functions\expect( 'get_option' )->andReturn( [] );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$obj = $this->get_sut();

		$this->expectOutputString( '<div><h3>Test</h3>No available translations found</div>' );
		$obj->widget( [], [ 'title' => 'Test' ] );
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

}
