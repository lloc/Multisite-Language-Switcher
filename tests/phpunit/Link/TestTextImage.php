<?php declare( strict_types=1 );

namespace lloc\MslsTests\Link;

use lloc\Msls\Link\TextImage;
use lloc\MslsTests\MslsUnitTestCase;

final class TestTextImage extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		$this->assertIsSTring( TextImage::get_description() );
	}
}
