<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsOptionsTaxTerm;

class WP_Test_MslsOptionsTaxTerm extends Msls_UnitTestCase {

	function test_object() {
		$obj = new MslsOptionsTaxTerm( 0 );
		$this->assertInternalType( 'string', $obj->get_postlink( '' ) );
	}

}
