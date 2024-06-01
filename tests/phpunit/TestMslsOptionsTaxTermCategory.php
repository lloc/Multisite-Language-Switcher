<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsTaxTermCategory;

class TestMslsOptionsTaxTermCategory extends MslsUnitTestCase {

	public function test_object(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$obj = new MslsOptionsTaxTermCategory( 0 );

		$this->assertIsSTring( $obj->get_postlink( '' ) );
	}
}
