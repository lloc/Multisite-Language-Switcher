<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryMonth;

class WP_Test_MslsOptionsQueryMonth extends Msls_UnitTestCase {

	public function get_sut(): MslsOptionsQueryMonth {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		return new MslsOptionsQueryMonth( 1998, 12 );
	}

	public function test_has_value(): void {
		// PostCounter will return 0 because WP_Query doesn't exist during tests
		$this->assertFalse( $this->get_sut()->has_value( 'de_DE' ) );
	}

	public function test_get_current_link_method(): void {
		$expected = 'https://example.org/queried-month';
		Functions\expect( 'get_month_link' )->once()->andReturn( $expected );

		$this->assertEquals( $expected, $this->get_sut()->get_current_link() );
	}

	public function test_get_date_query(): void {
		$expected = [ 'year' => 1998, 'month' => 12 ];

		$this->assertEquals( $expected, $this->get_sut()->get_date_query() );
	}

}
