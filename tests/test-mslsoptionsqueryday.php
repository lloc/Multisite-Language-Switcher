<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryDay;

/**
 * WP_Test_MslsOptionsQueryDay
 */
class WP_Test_MslsOptionsQueryDay extends Msls_UnitTestCase {

	function get_test() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		return new MslsOptionsQueryDay();
	}

	function test_has_value_method() {
		$obj = $this->get_test();

		$this->assertInternalType( 'boolean', $obj->has_value( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		Functions\expect( 'get_day_link' )->once()->andReturn( 'https://example.org/queried-day' );

		$obj = $this->get_test();

		$this->assertEquals( 'https://example.org/queried-day', $obj->get_current_link() );
	}

}
