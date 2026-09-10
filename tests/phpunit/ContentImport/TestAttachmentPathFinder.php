<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\AttachmentPathFinder;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestAttachmentPathFinder extends MslsUnitTestCase {

	/**
	 * Every row supplies the full column set: the trailing numbers are Mockery invocation
	 * counts for get_post_meta(), delete_post_meta() and get_blog_post().
	 *
	 * @return array<string, array{array<int, array<string, string>>, string, mixed, array<int, array<string, string>>, array<string, int>|null, int, int, int, object|null}>
	 */
	public static function filter_srcset_provider(): array {
		$image_src     = 'http://example.com/image.jpg';
		$sized_src     = 'http://example.com/image-300x300.jpg';
		$msls_imported = array(
			'blog' => 1,
			'post' => 1,
		);
		$source_post   = (object) array( 'guid' => 'http://example.com/image.jpg' );

		return array(
			'attachment id zero'                => array( array(), $image_src, 0, array(), null, 0, 0, 0, null ),
			'attachment id empty string'        => array( array(), $image_src, '', array(), null, 0, 0, 0, null ),
			'attachment id null'                => array( array(), $image_src, null, array(), null, 0, 0, 0, null ),
			'no import meta'                    => array( array(), $image_src, 1, array(), null, 1, 1, 0, null ),
			'import meta without blog and post' => array( array(), $image_src, 1, array(), array( 'random' => 'item' ), 1, 1, 0, null ),
			'source post gone'                  => array( array( array( 'url' => $image_src ) ), $image_src, 1, array( array( 'url' => $image_src ) ), $msls_imported, 1, 0, 1, null ),
			'source url rewritten to itself'    => array( array( array( 'url' => $image_src ) ), $image_src, 1, array( array( 'url' => $image_src ) ), $msls_imported, 1, 0, 1, $source_post ),
			'sized source url keeps its size'   => array( array( array( 'url' => $sized_src ) ), $image_src, 1, array( array( 'url' => $sized_src ) ), $msls_imported, 1, 0, 1, $source_post ),
		);
	}

	#[DataProvider( 'filter_srcset_provider' )]
	public function test_filter_srcset( $source, $imageSrc, $attachmentId, $expected, $msls_imported = null, $times_gpm = 0, $time_dpm = 0, $times_gbp = 0, $blog_post = null ) {
		Functions\expect( 'get_post_meta' )->times( $times_gpm )->andReturn( $msls_imported );
		Functions\expect( 'delete_post_meta' )->times( $time_dpm );
		Functions\expect( 'get_blog_post' )->times( $times_gbp )->andReturn( $blog_post );

		$test = new AttachmentPathFinder();

		$this->assertEquals( $expected, $test->filter_srcset( $source, null, $imageSrc, null, $attachmentId ) );
	}

	/**
	 * @return array<string, array{string, object, int}>
	 */
	public static function filter_attachment_url_provider(): array {
		$generic_obj = (object) array( 'guid' => 'http://example.com/image.jpg' );

		$post_mock       = \Mockery::mock( '\WP_Post' );
		$post_mock->guid = 'http://example.com/image.jpg';

		return array(
			'source post is not a WP_Post' => array( 'http://example.com/image.jpg', $generic_obj, 42 ),
			'no import data for the id'    => array( 'http://example.com/image.jpg', $generic_obj, 0 ),
			'source post is a WP_Post'     => array( 'http://example.com/image.jpg', $post_mock, 42 ),
		);
	}

	#[DataProvider( 'filter_attachment_url_provider' )]
	public function test_filter_attachment_url( string $image_src, $source_post, int $attachment_id ): void {
		$msls_imported = array(
			'blog' => 1,
			'post' => 1,
		);

		Functions\expect( 'get_post_meta' )->zeroOrMoreTimes()->andReturn( $msls_imported );
		Functions\expect( 'delete_post_meta' )->zeroOrMoreTimes();
		Functions\expect( 'get_blog_post' )->zeroOrMoreTimes()->andReturn( $source_post );

		$test = new AttachmentPathFinder();

		$this->assertEquals( $source_post->guid, $test->filter_attachment_url( $image_src, $attachment_id ) );
	}
}
