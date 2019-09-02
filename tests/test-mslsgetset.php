<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsGetSet;

class WP_Test_MslsGetSet extends Msls_UnitTestCase {

	public function test_class() {
		$obj = new MslsGetSet();

		$this->assertTrue( $obj->is_empty() );

		$obj->abc = 'test';

		$this->assertEquals( 'test', $obj->abc );
		$this->assertTrue( isset( $obj->abc ) );
		$this->assertFalse( $obj->is_empty() );

		unset( $obj->abc );
		$this->assertTrue( $obj->is_empty() );

		$obj->temp = 'test';

		$this->assertTrue( $obj->has_value( 'temp' ) );

		$this->assertEquals( array( 'temp' => 'test' ), $obj->get_arr() );

		$obj->reset();
		$this->assertEquals( array(), $obj->get_arr() );
	}

}
