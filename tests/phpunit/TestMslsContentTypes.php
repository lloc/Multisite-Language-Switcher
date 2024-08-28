<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsContentTypes;
use lloc\Msls\MslsPostType;

class TestMslsContentTypes extends MslsUnitTestCase {

	public function test_create(): void {
		Functions\expect( 'get_post_types' )->twice()->andReturn( array() );

		$obj = MslsContentTypes::create();

		$this->assertInstanceOf( MslsPostType::class, $obj );
	}

	public function test_is_taxonomy(): void {
		$this->assertFalse( MslsContentTypes::create()->is_taxonomy() );
	}
}
