<?php declare( strict_types=1 );

namespace lloc\MslsTests\Component\Icon;

use Brain\Monkey\Functions;
use lloc\Msls\Component\Icon\IconPng;
use lloc\MslsTests\MslsUnitTestCase;

final class TestIconPng extends MslsUnitTestCase {

	public function test_get(): void {
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 4 ) . '/' );

		$obj = new IconPng();

		$this->assertEquals( 'cz.png', $obj->get( 'cs_CZ' ) );
		$this->assertEquals( 'europeanunion.png', $obj->get( 'eo' ) );
		$this->assertEquals( 'catalonia.png', $obj->get( 'ca' ) );
		$this->assertEquals( 'ko.png', $obj->get( 'pinko' ) );
	}
}
