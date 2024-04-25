<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsRegistry;

class TestMslsRegistry extends Msls_UnitTestCase {

	function test_set_method() {
		$obj = new MslsRegistry();

		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', 1 );
		$this->assertEquals( 1, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', null );
		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
	}

}
