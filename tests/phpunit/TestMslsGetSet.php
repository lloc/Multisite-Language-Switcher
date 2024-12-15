<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use lloc\Msls\MslsGetSet;

final class TestMslsGetSet extends MslsUnitTestCase {

	public function test_unset(): void {
		$obj = new MslsGetSet();
		$this->assertTrue( $obj->is_empty() );

		$obj->abc = 'Test';
		$this->assertFalse( $obj->is_empty() );

		unset( $obj->abc );
		$this->assertTrue( $obj->is_empty() );
	}

	public function test_set_empty(): void {
		$obj = new MslsGetSet();
		$this->assertTrue( $obj->is_empty() );

		$obj->abc = 'Test';
		$this->assertFalse( $obj->is_empty() );

		$obj->abc = '';
		$this->assertTrue( $obj->is_empty() );
	}

	public function test_set(): void {
		$obj = new MslsGetSet();

		$obj->abc = 'test';

		$this->assertEquals( 'test', $obj->abc );
		$this->assertTrue( isset( $obj->abc ) );
	}

	public function test_isset(): void {
		$obj = new MslsGetSet();

		$this->assertTrue( $obj->is_empty() );

		$obj->abc = 'test';

		$this->assertTrue( isset( $obj->abc ) );
		$this->assertEquals( 'test', $obj->abc );
	}

	public function test_has_value(): void {
		$obj = new MslsGetSet();

		$obj->a_key = 'test';

		$this->assertTrue( $obj->has_value( 'a_key' ) );
	}

	public function test_get_array(): void {
		$obj = new MslsGetSet();

		$obj->temp = 'test';

		$this->assertEquals( array( 'temp' => 'test' ), $obj->get_arr() );

		$obj->reset();
		$this->assertEquals( array(), $obj->get_arr() );
	}
}
