<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLink;

class WP_Test_MslsLink extends Msls_UnitTestCase {

	public function test_get_types() {
		$this->assertCount( 4, MslsLink::get_types() );
	}

	public function test_get_description() {
		Functions\when( '__' )->returnArg();

		$this->assertEquals( 'Flag and description', MslsLink::get_description() );
	}

	public function test_get_types_description() {
		Functions\when( '__' )->returnArg();

		$this->assertCount( 4, MslsLink::get_types_description() );
	}

	public function test_callback() {
		$this->assertEquals( '{Test}', MslsLink::callback( 'Test' ) );
	}

	public function test_object2string_conversion() {
		$obj = MslsLink::create( 0 );

		$this->assertEquals( '<img src="{src}" alt="{alt}"/> {txt}', strval( $obj ) );
	}

}
