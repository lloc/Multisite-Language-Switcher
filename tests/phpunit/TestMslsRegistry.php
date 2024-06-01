<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use lloc\Msls\MslsRegistry;

class TestMslsRegistry extends MslsUnitTestCase {

	public function test_set_method(): void {
		$obj = new MslsRegistry();

		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', 1 );
		$this->assertEquals( 1, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', null );
		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
	}
}
