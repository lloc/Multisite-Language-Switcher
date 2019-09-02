<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsWidget;

class WP_Test_MslsWidget extends Msls_UnitTestCase {

	public function get_sut() {
		\Mockery::mock( '\WP_Widget' );

		return new MslsWidget();
	}

	function test_widget_method() {
		$obj = $this->get_sut();

		$this->expectOutputString( 'No available translations found' );
		$obj->widget( array(), array() );

		return $obj;
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
