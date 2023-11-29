<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkImageOnly;

class WP_Test_MslsLinkImageOnly extends Msls_UnitTestCase {

	function test_get_description_method() {
		Functions\when( '__' )->returnArg();

		$this->assertIsSTring( MslsLinkImageOnly::get_description() );
	}

}
