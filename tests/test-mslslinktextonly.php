<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextOnly;

class WP_Test_MslsLinkTextOnly extends Msls_UnitTestCase {

	function test_get_description_method() {
		Functions\when( '__' )->returnArg();

		$this->assertInternalType( 'string', MslsLinkTextOnly::get_description() );
	}

}
