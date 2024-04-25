<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTaxTermCategory;

class TestMslsOptionsTaxTermCategory extends Msls_UnitTestCase {

	/**
	 * Verify the check_url-method
	 */
	function test_object() {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$obj = new MslsOptionsTaxTermCategory( 0 );

		$this->assertIsSTring( $obj->get_postlink( '' ) );
	}

}
