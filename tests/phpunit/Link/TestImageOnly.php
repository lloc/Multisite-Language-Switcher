<?php declare( strict_types=1 );

namespace lloc\MslsTests\Link;

use lloc\Msls\Link\ImageOnly;
use lloc\MslsTests\MslsUnitTestCase;

final class TestImageOnly extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		$this->assertIsSTring( ImageOnly::get_description() );
	}
}
