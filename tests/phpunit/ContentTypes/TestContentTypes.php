<?php declare( strict_types=1 );

namespace lloc\MslsTests\ContentTypes;

use Brain\Monkey\Functions;
use lloc\Msls\ContentTypes\ContentTypes;
use lloc\Msls\ContentTypes\PostType;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestContentTypes extends MslsUnitTestCase {

	public function test_create(): void {
		Functions\expect( 'get_post_types' )->twice()->andReturn( array() );

		$obj = ContentTypes::create();

		$this->assertInstanceOf( PostType::class, $obj );
	}

	public function test_is_taxonomy(): void {
		Functions\expect( 'get_post_types' )->twice()->andReturn( array() );

		$this->assertFalse( ContentTypes::create()->is_taxonomy() );
	}
}
