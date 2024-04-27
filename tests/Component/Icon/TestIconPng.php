<?php

namespace lloc\MslsTests\Component\Icon;

use Brain\Monkey\Functions;
use lloc\MslsTests\MslsUnitTestCase;
use lloc\Msls\Component\Icon\IconPng;

class TestIconPng extends MslsUnitTestCase {

	public function test_get(): void {
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 3 ) . '/' );

		$obj = new IconPng();

		$this->assertEquals( 'cz.png', $obj->get( 'cs_CZ' ) );
		$this->assertEquals( 'europeanunion.png', $obj->get( 'eo' ) );
		$this->assertEquals( 'catalonia.png', $obj->get( 'ca' ) );
		$this->assertEquals( 'ko.png', $obj->get( 'pinko' ) );
	}

}
