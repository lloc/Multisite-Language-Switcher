<?php declare( strict_types=1 );

namespace lloc\MslsTests\Link;

use lloc\Msls\Link\TextOnly;
use lloc\MslsTests\MslsUnitTestCase;

final class TestTextOnly extends MslsUnitTestCase {

	public function test_get_description_method(): void {
		$this->assertIsSTring( TextOnly::get_description() );
	}
}
