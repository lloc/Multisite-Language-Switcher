<?php declare( strict_types=1 );

namespace lloc\MslsTests\Link;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Link\Link;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Filters;
use function Brain\Monkey\Functions;

final class TestLink extends MslsUnitTestCase {

	public function test_create(): void {
		Functions\expect( 'has_filter' )->once()->with( 'msls_link_create' )->andReturn( true );

		$obj     = new Link();
		$display = 0;

		Filters\expectApplied( 'msls_link_create' )->once()->andReturn( $obj );

		$obj = Link::create( $display );

		$this->assertInstanceOf( Link::class, $obj );
	}

	public function test_get_types(): void {
		$this->assertCount( 4, Link::get_types() );
	}

	public function test_get_description(): void {
		$this->assertEquals( 'Flag and description', Link::get_description() );
	}

	public function test_get_types_description(): void {
		$this->assertCount( 4, Link::get_types_description() );
	}

	public function test_callback(): void {
		$this->assertEquals( '{Test}', Link::callback( 'Test' ) );
	}

	public function test_object2string_conversion(): void {
		$obj = Link::create( 0 );

		$this->assertEquals( '<img src="{src}" alt="{alt}"/> {txt}', strval( $obj ) );
	}
}
