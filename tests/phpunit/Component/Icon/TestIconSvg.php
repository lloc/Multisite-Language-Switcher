<?php declare( strict_types=1 );

namespace lloc\MslsTests\Component\Icon;

use Brain\Monkey\Functions;
use lloc\Msls\Component\Icon\IconSvg;
use lloc\MslsTests\MslsUnitTestCase;

class TestIconSvg extends MslsUnitTestCase {

	public function test_get(): void {
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 4 ) . '/' );

		$obj = new IconSvg();

		$this->assertEquals( 'flag-icon-cz', $obj->get( 'cs_CZ' ) );
		$this->assertEquals( 'flag-icon-eu', $obj->get( 'eo' ) );
		$this->assertEquals( 'flag-icon-es-ca', $obj->get( 'ca' ) );
		$this->assertEquals( 'flag-icon-ko', $obj->get( 'pinko' ) );
	}
}
