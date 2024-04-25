<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextOnly;

class TestMslsLinkTextOnly extends Msls_UnitTestCase {

	function test_get_description_method() {
		Functions\when( '__' )->returnArg();

		$this->assertIsSTring( MslsLinkTextOnly::get_description() );
	}

}
