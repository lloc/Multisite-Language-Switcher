<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextImage;

final class TestMslsLinkTextImage extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		$this->assertIsSTring( MslsLinkTextImage::get_description() );
	}
}
