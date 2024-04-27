<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTaxTerm;

class TestMslsOptionsTaxTerm extends MslsUnitTestCase {

	public function test_object(): void {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$obj = new MslsOptionsTaxTerm( 0 );

		$this->assertIsSTring( $obj->get_postlink( '' ) );
	}

}
