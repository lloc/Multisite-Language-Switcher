<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTax;

/**
 * TestMslsOptionsTax
 */
class TestMslsOptionsTax extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$this->test = new MslsOptionsTax( 0 );
	}

	public function test_get_tax_query(): void {
		$this->assertIsString( $this->test->get_tax_query() );
	}

	public function test_get_postlink(): void {
		$this->assertIsString( $this->test->get_postlink( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		$this->assertIsString( $this->test->get_current_link() );
	}

	public function test_get_term_link(): void {
		$this->assertIsString( $this->test->get_term_link( 42 ) );
	}
}
