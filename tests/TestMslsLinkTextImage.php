<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextImage;

class TestMslsLinkTextImage extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		Functions\when( '__' )->returnArg();

		$this->assertIsSTring( MslsLinkTextImage::get_description() );
	}

}
