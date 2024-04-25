<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkImageOnly;

class TestMslsLinkImageOnly extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		Functions\when( '__' )->returnArg();

		$this->assertIsSTring( MslsLinkImageOnly::get_description() );
	}

}
