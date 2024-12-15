<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\MetaBox;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Functions;

final class TestMetaBox extends MslsUnitTestCase {

	public function test_render(): void {
		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 1;

		Functions\expect( 'get_post' )->once()->andReturn( $post );
		Functions\expect( 'get_option' )->twice()->andReturn( array() );
		Functions\expect( 'get_available_languages' )->once()->andReturn( array() );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'de_DE' );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( array() );

		$expected = '<p>No translated versions linked to this post: import content functionality is disabled.</p>';

		( new MetaBox() )->render();

		$this->expectOutputString( $expected );
	}

	public function test_print_modal_html(): void {
		$this->assertEquals( '', ( new MetaBox() )->print_modal_html() );
	}
}
