<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTax;

/**
 * TestMslsOptionsTax
 */
class TestMslsOptionsTax extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$this->test = new MslsOptionsTax( 0 );
	}

	public function test_get_tax_query_method(): void {
		$this->assertIsString( $this->test->get_tax_query() );
	}

	public function test_get_postlink_method(): void {
		$this->assertIsString( $this->test->get_postlink( 'de_DE' ) );
	}

	public function test_get_current_link_method(): void {
		$this->assertIsString( $this->test->get_current_link() );
	}

}
