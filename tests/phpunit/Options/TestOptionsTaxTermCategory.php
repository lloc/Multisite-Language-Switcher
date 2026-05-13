<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options;

use lloc\MslsTests\MslsUnitTestCase;

use Brain\Monkey\Functions;
use lloc\Msls\Options\OptionsTaxTermCategory;

final class TestOptionsTaxTermCategory extends MslsUnitTestCase {

	public function test_object(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$obj = new OptionsTaxTermCategory( 0 );

		$this->assertIsSTring( $obj->get_postlink( '' ) );
	}
}
