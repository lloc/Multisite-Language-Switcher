<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsOptionsQueryMonth;

class WP_Test_MslsOptionsQueryMonth extends Msls_UnitTestCase {

	function test_has_value_method() {
		$obj = new MslsOptionsQueryMonth();
		$this->assertInternalType( 'boolean', $obj->has_value( 'de_DE' ) );
		return $obj;
	}

	function test_get_current_link_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

}
