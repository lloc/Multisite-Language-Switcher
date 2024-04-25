<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsGetSet;

class TestMslsGetSet extends Msls_UnitTestCase {

	public function test_unset() {
		$obj = new MslsGetSet();
		$this->assertTrue( $obj->is_empty() );

		$obj->abc = 'Test';
		$this->assertFalse( $obj->is_empty() );

		unset( $obj->abc );
		$this->assertTrue( $obj->is_empty() );
	}

	public function test_set_empty() {
		$obj = new MslsGetSet();
		$this->assertTrue( $obj->is_empty() );

		$obj->abc = 'Test';
		$this->assertFalse( $obj->is_empty() );

		$obj->abc = '';
		$this->assertTrue( $obj->is_empty() );
	}

	public function test_set() {
		$obj = new MslsGetSet();

		$obj->abc = 'test';

		$this->assertEquals( 'test', $obj->abc );
		$this->assertTrue( isset( $obj->abc ) );
	}

	public function test_isset() {
		$obj = new MslsGetSet();

		$this->assertTrue( $obj->is_empty() );

		$obj->abc = 'test';

		$this->assertTrue( isset( $obj->abc ) );
		$this->assertEquals( 'test', $obj->abc );
	}

	public function test_has_value() {
		$obj = new MslsGetSet();

		$obj->a_key = 'test';

		$this->assertTrue( $obj->has_value( 'a_key' ) );
	}

	public function test_get_array() {
		$obj = new MslsGetSet();

		$obj->temp = 'test';

		$this->assertEquals( [ 'temp' => 'test' ], $obj->get_arr() );

		$obj->reset();
		$this->assertEquals( [], $obj->get_arr() );
	}

}
