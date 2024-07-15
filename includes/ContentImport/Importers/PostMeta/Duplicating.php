<?php

namespace lloc\Msls\ContentImport\Importers\PostMeta;

use lloc\Msls\ContentImport\Importers\BaseImporter;

class Duplicating extends BaseImporter {

	const TYPE = 'duplicating';

	/**
	 * Returns an array of information about the importer.
	 *
	 * @return \stdClass
	 */
	public static function info() {
		return (object) [
			'slug' => static::TYPE,
			'name' => __( 'Duplicating', 'multisite-language-switcher' ),
			'description' => __( 'Copies the source post meta to the destination.', 'multisite-language-switcher' )
		];
	}

	public function import( array $data ) {
		$source_blog_id = $this->import_coordinates->source_blog_id;
		$source_post_id = $this->import_coordinates->source_post_id;
		$dest_post_id   = $this->import_coordinates->dest_post_id;

		switch_to_blog( $source_blog_id );
		$source_meta = get_post_custom( $source_post_id );

		switch_to_blog( $this->import_coordinates->dest_blog_id );

		$source_meta = $this->filter_post_meta( $source_meta );

		foreach ( $source_meta as $key => $entries ) {
			delete_post_meta( $dest_post_id, $key );
			foreach ( $entries as $entry ) {
				$entry = maybe_unserialize( $entry );
				add_post_meta( $dest_post_id, $key, $entry );
				$this->logger->log_success( 'meta/added', array( $key => $entry ) );
			}
		}

		wp_cache_delete( $dest_post_id, 'post_meta' );

		restore_current_blog();

		return $data;
	}

	public function filter_post_meta( array $meta ) {
		$blacklist = array( '_edit_last', '_thumbnail_id', '_edit_lock' );

		/**
		 * Filters the list of post meta that should not be imported for a post.
		 *
		 * @param array $blacklist
		 * @param array $meta
		 * @param ImportCoordinates $import_coordinates
		 */
		$blacklist = apply_filters( 'msls_content_import_post_meta_blacklist',
			$blacklist,
			$meta,
			$this->import_coordinates );

		return array_diff_key( $meta, array_combine( $blacklist, $blacklist ) );
	}
}