<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQuery;

/**
 * WP_Test_MslsOptionsQuery
 */
class WP_Test_MslsOptionsQuery extends Msls_UnitTestCase {

	function get_test() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		return new MslsOptionsQuery();
	}

	function test_get_current_link_method() {
		Functions\expect( 'home_url' )->once()->andReturn( 'https://example.org/queried-object' );

		$obj = $this->get_test();

		$this->assertEquals( 'https://example.org/queried-object', $obj->get_current_link() );
	}

}
