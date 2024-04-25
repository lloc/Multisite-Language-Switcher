<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextImage;

class TestMslsLinkTextImage extends Msls_UnitTestCase {

	function test_get_description_method() {
		Functions\when( '__' )->returnArg();

		$this->assertIsSTring( MslsLinkTextImage::get_description() );
	}

}
