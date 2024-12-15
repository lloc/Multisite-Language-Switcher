<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkTextOnly;

final class TestMslsLinkTextOnly extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		$this->assertIsSTring( MslsLinkTextOnly::get_description() );
	}
}
