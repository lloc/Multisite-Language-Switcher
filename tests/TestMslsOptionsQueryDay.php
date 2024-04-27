<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryDay;

/**
 * TestMslsOptionsQueryDay
 */
class TestMslsOptionsQueryDay extends MslsUnitTestCase {

	public function setUp(): void {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->test = new MslsOptionsQueryDay();
	}

	public function test_has_value(): void {
		$this->assertTrue( $this->test->has_value( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_day_link' )->once()->andReturn( 'https://example.org/queried-day' );

		$this->assertEquals( 'https://example.org/queried-day', $this->test->get_current_link() );
	}

}
