<?php

namespace lloc\MslsTests\ContentImport\Importers\PostMeta;

use Brain\Monkey\Functions;
use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\PostMeta\Duplicating;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

class TestDuplicating extends MslsUnitTestCase {

	public function testImport(): void {
		Functions\expect( 'switch_to_blog' )->twice();
		Functions\expect( 'get_post_custom' )->once()->andReturn( array( 88 => array( 'foo' => 'bar' ) ) );
		Functions\expect( 'wp_cache_delete' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'delete_post_meta' )->once();
		Functions\expect( 'maybe_unserialize' )->once()->andReturnFirstArg();
		Functions\expect( 'add_post_meta' )->once();

		$coordinates                 = \Mockery::mock( ImportCoordinates::class );
		$coordinates->source_blog_id = 1;
		$coordinates->source_post_id = 42;
		$coordinates->dest_blog_id   = 2;
		$coordinates->dest_post_id   = 13;

		$this->assertEquals( array(), ( new Duplicating( $coordinates ) )->import( array() ) );
	}
}
