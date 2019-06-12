<?php

namespace lloc\Msls\ContentImport\Importers\PostFields;


use lloc\Msls\ContentImport\TestCase;

class DuplicatingTest extends TestCase {
	/**
	 * It should duplicate post fields from the source post to the destination
	 *
	 * @test
	 */
	public function should_duplicate_post_fields_from_the_source_post_to_the_destination() {
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();

		restore_current_blog();

		$obj     = new Duplicating( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$source_post_data     = (array) $import_coordinates->source_post;
		$imported_fields      = array_flip( $obj->filter_fields() );
		$fields_to_import     = array_intersect_key( $source_post_data, $imported_fields );
		$fields_not_to_import = array_diff_key( $dest_post_data, $imported_fields );

		$mutated = $obj->import( $dest_post_data );

		foreach ( $fields_to_import as $field => $value ) {
			$this->assertEquals( $value, $mutated[ $field ] );
		}

		foreach ( $fields_not_to_import as $field => $value ) {
			$this->assertEquals( $dest_post_data[ $field ], $mutated[ $field ] );
		}
	}
}
