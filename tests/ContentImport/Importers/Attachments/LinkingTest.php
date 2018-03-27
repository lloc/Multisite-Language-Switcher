<?php

namespace lloc\Msls\ContentImport\Importers\Attachments;


use lloc\Msls\ContentImport\TestCase;

class LinkingTest extends TestCase {
	/**
	 * It should not create an attachment in the destination site
	 *
	 * @test
	 */
	public function should_not_create_an_attachment_in_the_destination_site() {
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();
		$dest_post_id = $import_coordinates->dest_post_id;

		switch_to_blog( $import_coordinates->source_blog_id );
		$this->factory()->attachment->create_upload_object( msls_test_data( 'images/image-one.jpg' ) );

		// the import functionality should work starting fron any blog context
		restore_current_blog();

		$obj = new Linking( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$mutated = $obj->import( $dest_post_data );

		$this->assertEmpty( get_attached_media( 'image', $dest_post_id ) );
		$this->assertEquals( $mutated, $dest_post_data );
	}
}
