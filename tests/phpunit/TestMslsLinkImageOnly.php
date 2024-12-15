<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLinkImageOnly;

final class TestMslsLinkImageOnly extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		$this->assertIsSTring( MslsLinkImageOnly::get_description() );
	}
}
