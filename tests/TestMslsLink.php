<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsLink;

class TestMslsLink extends MslsUnitTestCase {

	public function test_get_types(): void {
		$this->assertCount( 4, MslsLink::get_types() );
	}

	public function test_get_description(): void {
		Functions\when( '__' )->returnArg();

		$this->assertEquals( 'Flag and description', MslsLink::get_description() );
	}

	public function test_get_types_description(): void {
		Functions\when( '__' )->returnArg();

		$this->assertCount( 4, MslsLink::get_types_description() );
	}

	public function test_callback(): void {
		$this->assertEquals( '{Test}', MslsLink::callback( 'Test' ) );
	}

	public function test_object2string_conversion(): void {
		$obj = MslsLink::create( 0 );

		$this->assertEquals( '<img src="{src}" alt="{alt}"/> {txt}', strval( $obj ) );
	}

}
