<?php

namespace lloc\Msls\ContentImport\Importers\PostMeta;


use lloc\Msls\ContentImport\TestCase;

class DuplicatingTest extends TestCase {
	/**
	 * It should duplicate the source post meta to the destination post
	 *
	 * @test
	 */
	public function should_duplicate_the_source_post_meta_to_the_destination_post() {
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();
		$source_post_id = $import_coordinates->source_post_id;
		$dest_post_id   = $import_coordinates->dest_post_id;

		$obj = new Duplicating( $import_coordinates, $logger->reveal(), $relations->reveal() );

		switch_to_blog( $import_coordinates->source_blog_id );
		update_post_meta( $source_post_id, 'foo', 'bar' );
		update_post_meta( $source_post_id, 'bar', [ 23, 89 ] );
		add_post_meta( $source_post_id, 'multi', 'one' );
		add_post_meta( $source_post_id, 'multi', 'two' );
		$source_post_meta = get_post_meta( $source_post_id );
		switch_to_blog( $import_coordinates->dest_blog_id );
		update_post_meta( $dest_post_id, 'foo', 'not-bar' );
		update_post_meta( $dest_post_id, 'multi', 'another-value' );
		$dest_post_meta = get_post_meta( $import_coordinates->dest_post_id );
		add_filter( 'msls_content_import_post_meta_blacklist', function ( array $meta ) {
			$meta[] = 'foo';

			return $meta;
		} );

		restore_current_blog();

		$imported_fields    = $obj->filter_post_meta( $source_post_meta );
		$meta_to_import     = array_intersect_key( $source_post_meta, $imported_fields );
		$meta_not_to_import = array_diff_key( $dest_post_meta, $imported_fields );

		$mutated_data = $obj->import( $dest_post_data );

		$this->assertEqualSets( $dest_post_data, $mutated_data );

		switch_to_blog( $import_coordinates->dest_blog_id );
		$mutated_meta = get_post_meta( $import_coordinates->dest_post_id );
		restore_current_blog();

		foreach ( $meta_to_import as $field => $value ) {
			$this->assertEquals( $value, $mutated_meta[ $field ] );
		}

		foreach ( $meta_not_to_import as $field => $value ) {
			$this->assertEquals( $dest_post_meta[ $field ], $mutated_meta[ $field ] );
		}
	}
}
