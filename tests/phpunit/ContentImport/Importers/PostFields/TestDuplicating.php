<?php

namespace lloc\MslsTests\ContentImport\Importers\PostFields;

use Brain\Monkey\Functions;
use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\PostFields\Duplicating;
use lloc\MslsTests\MslsUnitTestCase;

final class TestDuplicating extends MslsUnitTestCase {

	public function testImport(): void {
		Functions\expect( 'wp_insert_post' )->once();

		$post                        = \Mockery::mock( \WP_Post::class );
		$post->post_excerpt          = 'excerpt';
		$post->post_title            = 'title';
		$post->post_content          = 'content';
		$post->post_content_filtered = 'content_filtered';

		$coordinates              = \Mockery::mock( ImportCoordinates::class );
		$coordinates->source_post = $post;

		$result = array(
			'post_type'             => 'post',
			'post_content'          => 'content',
			'post_content_filtered' => 'content_filtered',
			'post_title'            => 'title',
			'post_excerpt'          => 'excerpt',
		);

		$this->assertEquals( $result, ( new Duplicating( $coordinates ) )->import( array() ) );
	}

	public function testFilterFields(): void {
		$coordinates = \Mockery::mock( ImportCoordinates::class );

		$test = new Duplicating( $coordinates );

		$result = array(
			'post_content',
			'post_content_filtered',
			'post_title',
			'post_excerpt',
		);

		$this->assertEquals( $result, $test->filter_fields() );
	}
}
