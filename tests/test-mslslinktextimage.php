<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextImage;

class WP_Test_MslsLinkTextImage extends Msls_UnitTestCase {

	function test_get_description_method() {
		Functions\when( '__' )->returnArg();

		$this->assertInternalType( 'string', MslsLinkTextImage::get_description() );
	}

}
