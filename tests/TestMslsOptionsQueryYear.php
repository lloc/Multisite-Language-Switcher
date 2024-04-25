<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryYear;

class TestMslsOptionsQueryYear extends MslsUnitTestCase {

	protected function setUp(): void {
        parent::setUp();

        Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->test = new MslsOptionsQueryYear();
	}

	function test_has_value_method() {
		$this->assertIsBool( $this->test->has_value( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		Functions\expect( 'get_year_link' )->once()->andReturn( 'https://example.org/queried-year' );

		$this->assertEquals( 'https://example.org/queried-year', $this->test->get_current_link() );
	}

}
