<?php

namespace lloc\Msls\ContentImport\Importers\PostThumbnail;


use lloc\Msls\ContentImport\AttachmentPathFinder;
use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Service;
use lloc\Msls\ContentImport\TestCase;

class LinkingTest extends TestCase {
	/**
	 * It should create an attachment for the post thumbnail in the destination blog
	 *
	 * @test
	 */
	public function should_create_an_attachment_for_the_post_thumbnail_in_the_destination_blog() {
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();
		/** @var ImportCoordinates $import_coordinates */
		$dest_post_id = $import_coordinates->dest_post_id;

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_thumbnail_id = $this->factory()->attachment->create_upload_object( msls_test_data( 'images/image-one.jpg' ) );
		set_post_thumbnail( $import_coordinates->source_post, $source_thumbnail_id );

		// the import functionality should work starting fron any blog context
		restore_current_blog();

		$obj = new Linking( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$mutated = $obj->import( $dest_post_data );

		$this->assertEquals( $mutated, $dest_post_data );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$attached_media = get_attached_media( 'image', $dest_post_id );
		$this->assertCount( 1, $attached_media );
		$post_thumbnail_id = array_keys( $attached_media )[0];
		$this->assertEquals( get_post_thumbnail_id( $dest_post_id ), $post_thumbnail_id );
		$this->assertEquals( [
			'blog' => $import_coordinates->source_blog_id,
			'post' => $source_thumbnail_id,
		], get_post_meta( $post_thumbnail_id, AttachmentPathFinder::IMPORTED, true ) );
	}

	/**
	 * It should replace the existing post thumbnail if already set
	 *
	 * @test
	 */
	public function should_replace_the_existing_post_thumbnail_if_already_set() {
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();
		/** @var ImportCoordinates $import_coordinates */
		$dest_post_id = $import_coordinates->dest_post_id;

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_thumbnail_id = $this->factory()->attachment->create_upload_object( msls_test_data( 'images/image-one.jpg' ) );
		set_post_thumbnail( $import_coordinates->source_post, $source_thumbnail_id );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$existing_dest_post_thumbnail_id = $this->factory()->attachment->create_upload_object( msls_test_data( 'images/image-one.jpg' ) );
		set_post_thumbnail( $dest_post_id, $existing_dest_post_thumbnail_id );

		// the import functionality should work starting fron any blog context
		restore_current_blog();

		$obj = new Linking( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$mutated = $obj->import( $dest_post_data );

		$this->assertEquals( $mutated, $dest_post_data );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$attached_media = get_attached_media( 'image', $dest_post_id );
		$this->assertCount( 1, $attached_media );
		$this->assertNotEquals( get_post_thumbnail_id( $dest_post_id ), $existing_dest_post_thumbnail_id );
		$post_thumbnail_id = array_keys( $attached_media )[0];
		$this->assertEquals( get_post_thumbnail_id( $dest_post_id ), $post_thumbnail_id );
		$this->assertEquals( [
			'blog' => $import_coordinates->source_blog_id,
			'post' => $source_thumbnail_id,
		], get_post_meta( $post_thumbnail_id, AttachmentPathFinder::IMPORTED, true ) );
	}

	/**
	 * It should not re-create the post thumbnail attachment if already linked
	 *
	 * @test
	 */
	public function should_not_re_create_the_post_thumbnail_attachment_if_already_linked() {
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();
		/** @var ImportCoordinates $import_coordinates */
		$dest_post_id = $import_coordinates->dest_post_id;

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_thumbnail_id = $this->factory()->attachment->create_upload_object( msls_test_data( 'images/image-one.jpg' ) );
		set_post_thumbnail( $import_coordinates->source_post, $source_thumbnail_id );

		// the import functionality should work starting fron any blog context
		restore_current_blog();

		$obj = new Linking( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$obj->import( $dest_post_data );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$post_thumbnail_id_after_first_import = get_post_thumbnail_id($dest_post_id);

		$obj->import( $dest_post_data );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$this->assertEquals( $post_thumbnail_id_after_first_import, get_post_thumbnail_id( $dest_post_id ) );
	}

	/**
	 * It should not duplicate the post thumbnail files
	 *
	 * @test
	 */
	public function should_not_duplicate_the_post_thumbnail_files() {
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_thumbnail_id = $this->factory()->attachment->create_upload_object( msls_test_data( 'images/image-one.jpg' ) );
		set_post_thumbnail( $import_coordinates->source_post, $source_thumbnail_id );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$uploads_folder    = wp_upload_dir();
		// in tests the path might contain double `/`, a test issue
		$path        = str_replace( '//', '/', $uploads_folder['path'] );
		$this->assertEmpty( glob( $path . '/image-one*.jpg' ) );

		// the import functionality should work starting fron any blog context
		restore_current_blog();

		$obj = new Linking( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$obj->import( $dest_post_data );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$uploads_folder    = wp_upload_dir();
		// in tests the path might contain double `/`, a test issue
		$path        = str_replace( '//', '/', $uploads_folder['path'] );
		$this->assertEmpty( glob( $path . '/image-one*.jpg' ) );
	}

	/**
	 * It should return the source post attachment URLs in HTML
	 *
	 * @test
	 */
	public function should_return_the_source_post_attachment_ur_ls_in_html() {
		Service::instance()->hook();

		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_thumbnail_id = $this->factory()->attachment->create_upload_object( msls_test_data( 'images/image-one.jpg' ) );
		set_post_thumbnail( $import_coordinates->source_post, $source_thumbnail_id );
		$source_image_src    = wp_get_attachment_image_src( $source_thumbnail_id );
		$source_image_srcset = wp_get_attachment_image_srcset( $source_thumbnail_id );

		// the import functionality should work starting fron any blog context
		restore_current_blog();

		$obj = new Linking( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$obj->import( $dest_post_data );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$dest_post_thumbnail_id = get_post_thumbnail_id( $import_coordinates->dest_post_id );
		$this->assertEquals( $source_image_src, wp_get_attachment_image_src( $dest_post_thumbnail_id ) );
		$this->assertEquals( $source_image_srcset, wp_get_attachment_image_srcset( $dest_post_thumbnail_id ) );
	}
}
