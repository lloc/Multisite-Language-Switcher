<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTax;

/**
 * TestMslsOptionsTax
 */
class TestMslsOptionsTax extends Msls_UnitTestCase {

	function get_test() {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		return new MslsOptionsTax( 0 );
	}

	function test_get_tax_query_method() {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_tax_query() );
	}

	function test_get_postlink_method() {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_tax_query() );
		$this->assertIsSTring( $obj->get_postlink( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_tax_query() );
		$this->assertIsSTring( $obj->get_current_link() );
	}

}
