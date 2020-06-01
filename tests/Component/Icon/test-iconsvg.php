<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\Component\Icon\IconSvg;

class WP_Test_IconSvg extends Msls_UnitTestCase {

	public function test_get() {
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 3 ) . '/' );

		$obj = new IconSvg();

		$this->assertEquals( 'flag-icon-cz', $obj->get( 'cs_CZ' ) );
		$this->assertEquals( 'flag-icon-eu', $obj->get( 'eo' ) );
		$this->assertEquals( 'flag-icon-es-ca', $obj->get( 'ca' ) );
		$this->assertEquals( 'flag-icon-ko', $obj->get( 'pinko' ) );
	}

}
