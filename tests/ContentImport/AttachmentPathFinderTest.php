<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\AttachmentPathFinder as Finder;

class AttachmentPathFinderTest extends \Msls_UnitTestCase {

	/**
	 * @test
	 * it should be instantiatable
	 */
	public function it_should_be_instantiatable() {
		$sut = $this->make_instance();

		$this->assertInstanceOf( Finder::class, $sut );
	}

	/**
	 * @return Finder
	 */
	private function make_instance() {
		return new Finder();
	}

	/**
	 * Test filter_src with empty image date
	 */
	public function test_filter_src_with_empty_image_date() {
		$sut = $this->make_instance();
		$id  = $this->factory->attachment->create();

		$filtered = $sut->filter_src( [], $id );

		$this->assertEquals( [], $filtered );
	}

	/**
	 * Test filter_src with not linked attachment
	 */
	public function test_filter_sic_with_not_linked_attachment() {
		$sut   = $this->make_instance();
		$id    = $this->factory->attachment->create();
		$input = [ 'http://example.com/images/image.jpg' ];

		$filtered = $sut->filter_src( $input, $id );

		$this->assertEquals( $input, $filtered );
	}

	/**
	 * Test filter_src with bad linked attachment data
	 */
	public function test_filter_src_with_bad_linked_attachment_data() {
		$sut = $this->make_instance();
		$id  = $this->factory->attachment->create();
		add_post_meta( $id, Finder::IMPORTED, 'foo-bar' );
		$input = [ 'http://example.com/images/image.jpg' ];

		$filtered = $sut->filter_src( $input, $id );

		$this->assertEquals( $input, $filtered );
		$this->assertEquals( '', get_post_meta( $id, Finder::IMPORTED, true ), 'The bad meta should be deleted.' );
	}

	/**
	 * Test filter_src with good format meta but bad data
	 */
	public function test_filter_src_with_good_format_meta_but_bad_data() {
		$sut = $this->make_instance();
		$id  = $this->factory->attachment->create();
		add_post_meta( $id, Finder::IMPORTED, [ 'glob' => 23, 'bar' => 89 ] );
		$input = [ 'http://example.com/images/image.jpg' ];

		$filtered = $sut->filter_src( $input, $id );

		$this->assertEquals( $input, $filtered );
		$this->assertEquals( '', get_post_meta( $id, Finder::IMPORTED, true ), 'The bad meta should be deleted.' );
	}

	/**
	 * Test filter_src with imported image
	 */
	public function test_filter_src_with_imported_image() {
		$sut         = $this->make_instance();
		$id          = $this->factory->attachment->create();
		$source_blog = $this->factory->blog->create();
		switch_to_blog( $source_blog );
		$source_post = $this->factory->attachment->create_and_get();
		restore_current_blog();
		add_post_meta( $id, Finder::IMPORTED, [ 'blog' => $source_blog, 'post' => $source_post->ID ] );
		$input = [ 'http://example.com/images/image.jpg' ];

		$filtered = $sut->filter_src( $input, $id );

		$this->assertEquals( [ $source_post->guid ], $filtered );
	}


	/**
	 * Test filter_srcset with bad data
	 */
	public function test_filter_srcset_with_bad_data() {
		$sut = $this->make_instance();
		$id  = $this->factory->attachment->create();
//		$source_blog = $this->factory->blog->create();
//		switch_to_blog( $source_blog );
//		$source_post = $this->factory->attachment->create_and_get();
//		restore_current_blog();
		add_post_meta( $id, Finder::IMPORTED, [ 'foo' ] );
		$sources = [
			[
				'url' => 'http://example.com/images/image.jpg'
			]
		];

		$filtered = $sut->filter_srcset( $sources, [], 'example.jpg', [], $id );

		$this->assertEquals( $sources, $filtered );
	}

	/**
	 * Test filter_srcset
	 */
	public function test_filter_srcset() {
		$sut         = $this->make_instance();
		$id          = $this->factory->attachment->create();
		$source_blog = $this->factory->blog->create();
		switch_to_blog( $source_blog );
		$source_post = $this->factory->attachment->create_and_get();
		restore_current_blog();
		add_post_meta( $id, Finder::IMPORTED, [ 'blog' => $source_blog, 'post' => $source_post ] );
		$sources = [
			[
				'url' => 'http://blog1.example.com/image-100x100.jpg',
			],
			[
				'url' => 'http://blog1.example.com/image-150x300.jpg',
			]
		];

		$filtered = $sut->filter_srcset( $sources, [], 'http://example.com/images/source-image.jpg', [], $id );

		$this->assertEquals( [
			[
				'url' => 'http://example.com/images/source-image-100x100.jpg',
			],
			[
				'url' => 'http://example.com/images/source-image-150x300.jpg',
			]
		], $filtered );
	}
}
