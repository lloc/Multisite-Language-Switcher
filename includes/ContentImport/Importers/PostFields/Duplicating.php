<?php

namespace lloc\Msls\ContentImport\Importers\PostFields;

use lloc\Msls\ContentImport\Importers\BaseImporter;

/**
 * Class Duplicating
 *
 * Overwrites the destination post fields with an exact copy of the source post fields.
 *
 * @package lloc\Msls\ContentImport\Importers\PostFields
 */
class Duplicating extends BaseImporter {

	public function import( array $data ) {
		$source_post = $this->import_coordinates->source_post;

		$fields = $this->filter_fields();

		foreach ( $fields as $field ) {
			$value          = $source_post->{$field};
			$data[ $field ] = $value;
			$this->logger->log_success( 'post-field/added', [ $field => $value ] );
		}

		return $data;
	}

	protected function filter_fields() {
		$fields = array(
			'post_content',
			'post_content_filtered',
			'post_title',
			'post_excerpt',
		);

		/**
		 * Filters the list of post fields that should be imported for a post.
		 *
		 * @since TBD
		 *
		 * @param array             $blacklist
		 * @param ImportCoordinates $import_coordinates
		 */
		$fields = apply_filters( 'msls_content_import_post_fields_whitelist', $fields, $this->import_coordinates );

		return $fields;
	}
}