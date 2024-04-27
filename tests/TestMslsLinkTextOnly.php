<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextOnly;

class TestMslsLinkTextOnly extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		Functions\when( '__' )->returnArg();

		$this->assertIsSTring( MslsLinkTextOnly::get_description() );
	}

}
