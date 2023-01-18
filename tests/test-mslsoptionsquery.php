<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQuery;

/**
 * WP_Test_MslsOptionsQuery
 */
class WP_Test_MslsOptionsQuery extends Msls_UnitTestCase {

	public function test_get_current_link_method(): void {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );
		Functions\expect( 'home_url' )->once()->andReturnFirstArg();

		$sut = new MslsOptionsQuery();

		$this->assertEquals( '/', $sut->get_current_link() );
	}

	public function test_get_existing_postlink() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );
		Functions\expect( 'home_url' )->once()->andReturnFirstArg();

		$sut = new MslsOptionsQuery();

		$this->assertEquals( '/', $sut->get_postlink( 'de_DE' ) );
	}

	public function test_get_non_existing_postlink() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$sut = new MslsOptionsQuery();

		$this->assertEquals( '', $sut->get_postlink( 'it_IT' ) );
	}

}
