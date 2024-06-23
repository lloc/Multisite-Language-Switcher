<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\AttachmentPathFinder;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Functions;

class TestAttachmentPathFinder extends MslsUnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$this->test = new AttachmentPathFinder();
	}

	public function dataprovider_filter_srcset() {
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

		$this->assertEquals( $expected, $this->test->filter_srcset( $source, null, $imageSrc, null, $attachmentId ) );
	}
}
