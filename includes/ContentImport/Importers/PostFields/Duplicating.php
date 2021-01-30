<?php

namespace lloc\Msls\ContentImport\Importers\PostFields;

use lloc\Msls\ContentImport\Importers\BaseImporter;
use lloc\Msls\ContentImport\Importers\WithRequestPostAttributes;

/**
 * Class Duplicating
 *
 * Overwrites the destination post fields with an exact copy of the source post fields.
 *
 * @package lloc\Msls\ContentImport\Importers\PostFields
 */
class Duplicating extends BaseImporter {
	use WithRequestPostAttributes;

	const TYPE = 'duplicating';

	/**
	 * Returns an array of information about the importer.
	 *
	 * @return \stdClass
	 */
	public static function info() {
		return (object) [
			'slug'        => static::TYPE,
			'name'        => __( 'Duplicating', 'multisite-language-switcher' ),
			'description' => __( 'Copies the source post fields to the destination.', 'multisite-language-switcher' )
		];
	}

	public function import( array $data ) {
		// Set the post type reading it from the request payload, if not possible, use the default one.
		$data['post_type'] = $this->read_post_type_from_request( 'post' );

		$source_post = $this->import_coordinates->source_post;

		foreach ( $this->filter_fields() as $field ) {
			$value          = $source_post->{$field};
			$data[ $field ] = $value;
			$this->logger->log_success( 'post-field/added', [ $field => $value ] );
		}

		if ( ! doing_action( 'wp_insert_post_data' ) ) {
			$postarr = array_merge( $data, [ 'ID' => $this->import_coordinates->dest_post_id ] );
			wp_insert_post( $postarr );
		}

		return $data;
	}

	/**
	 * Filters the post fields that should be duplicated from the source post to the destination one.
	 *
	 * @return array
	 */
	public function filter_fields() {
		$fields = array(
			'post_content',
			'post_content_filtered',
			'post_title',
			'post_excerpt',
		);

		/**
		 * Filters the list of post fields that should be imported for a post.
		 *
		 * @param array $blacklist
		 * @param ImportCoordinates $import_coordinates
		 */
		$fields = apply_filters( 'msls_content_import_post_fields_whitelist', $fields, $this->import_coordinates );

		return $fields;
	}
}
