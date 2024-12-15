<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\AttachmentPathFinder;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Functions;

final class TestAttachmentPathFinder extends MslsUnitTestCase {

	public static function dataprovider_filter_srcset(): array {
		$image_src     = 'http://example.com/image.jpg';
		$msls_imported = array(
			'blog' => 1,
			'post' => 1,
		);
		$source_post   = (object) array( 'guid' => 'http://example.com/image.jpg' );

		return array(
			array( array(), $image_src, 0, array() ),
			array( array(), $image_src, '', array() ),
			array( array(), $image_src, null, array() ),
			array( array(), $image_src, 1, array(), null, 1, 1 ),
			array( array(), $image_src, 1, array(), array( 'random' => 'item' ), 1, 1 ),
			array( array( array( 'url' => $image_src ) ), $image_src, 1, array( array( 'url' => $image_src ) ), $msls_imported, 1, 0, 1 ),
			array( array( array( 'url' => $image_src ) ), $image_src, 1, array( array( 'url' => $image_src ) ), $msls_imported, 1, 0, 1, $source_post ),
			array( array( array( 'url' => 'http://example.com/image-300x300.jpg' ) ), $image_src, 1, array( array( 'url' => 'http://example.com/image-300x300.jpg' ) ), $msls_imported, 1, 0, 1, $source_post ),
		);
	}

	/**
	 * @dataProvider dataprovider_filter_srcset
	 */
	public function test_filter_srcset( $source, $imageSrc, $attachmentId, $expected, $msls_imported = null, $times_gpm = 0, $time_dpm = 0, $times_gbp = 0, $blog_post = false ) {
		Functions\expect( 'get_post_meta' )->times( $times_gpm )->andReturn( $msls_imported );
		Functions\expect( 'delete_post_meta' )->times( $time_dpm );
		Functions\expect( 'get_blog_post' )->times( $times_gbp )->andReturn( $blog_post );

		$test = new AttachmentPathFinder();

		$this->assertEquals( $expected, $test->filter_srcset( $source, null, $imageSrc, null, $attachmentId ) );
	}

	public static function dataprovider_filter_attachement_url(): array {
		$generic_obj = (object) array( 'guid' => 'http://example.com/image.jpg' );

		$post_mock       = \Mockery::mock( '\WP_Post' );
		$post_mock->guid = 'http://example.com/image.jpg';

		return array(
			array( 'http://example.com/image.jpg', $generic_obj, 42 ),
			array( 'http://example.com/image.jpg', $generic_obj, 0 ),
			array( 'http://example.com/image.jpg', $post_mock, 42 ),
		);
	}

	/**
	 * @dataProvider dataprovider_filter_attachement_url
	 */
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
