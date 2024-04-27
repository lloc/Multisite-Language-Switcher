<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryMonth;

class TestMslsOptionsQueryMonth extends MslsUnitTestCase {

	public function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->test = new MslsOptionsQueryMonth();
	}

	public function test_has_value(): void {
		$this->assertIsBool( $this->test->has_value( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_month_link' )->once()->andReturn( 'https://example.org/queried-month' );

		$this->assertEquals( 'https://example.org/queried-month', $this->test->get_current_link() );
	}

}
