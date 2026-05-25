<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Tax;

use Brain\Monkey\Functions;
use lloc\Msls\Options\Tax\Category;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestCategory extends MslsUnitTestCase {

	public function test_object(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$obj = new Category( 0 );

		$this->assertIsSTring( $obj->get_postlink( '' ) );
	}
}
