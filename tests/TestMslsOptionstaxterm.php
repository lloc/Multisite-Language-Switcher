<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTaxTerm;

class TestMslsOptionsTaxTerm extends Msls_UnitTestCase {

	function test_object() {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$obj = new MslsOptionsTaxTerm( 0 );

		$this->assertIsSTring( $obj->get_postlink( '' ) );
	}

}
