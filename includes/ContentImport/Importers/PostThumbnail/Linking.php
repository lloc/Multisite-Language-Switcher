<?php

namespace lloc\Msls\ContentImport\Importers\PostThumbnail;

use lloc\Msls\ContentImport\AttachmentPathFinder;
use lloc\Msls\ContentImport\Importers\BaseImporter;

/**
 * Class Linking
 *
 * Creates an attachment post for the post thumbnail in the destination blog without duplicating the attachment files.
 *
 * @package lloc\Msls\ContentImport\Importers\PostThumbnail
 */
class Linking extends BaseImporter {

	const TYPE = 'linking';

	/**
	 * Returns an array of information about the importer.
	 *
	 * @return \stdClass
	 */
	public static function info() {
		return (object) array(
			'slug'        => static::TYPE,
			'name'        => __( 'Linking', 'multisite-language-switcher' ),
			'description' => __(
				'Links the featured image from the source post to the destination post; the image is not duplicated.',
				'multisite-language-switcher'
			),
		);
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @return array<string, mixed>
	 */
	public function import( array $data ) {
		$source_blog_id = $this->import_coordinates->source_blog_id;
		$source_post_id = $this->import_coordinates->source_post_id;
		$dest_post_id   = $this->import_coordinates->dest_post_id;

		switch_to_blog( $source_blog_id );

		$source_post_thumbnail_id         = (int) get_post_thumbnail_id( $source_post_id );
		$source_post_thumbnail_attachment = get_post( $source_post_thumbnail_id );
		$source_post_thumbnail_meta       = $source_post_thumbnail_attachment instanceof \WP_Post ?
			$this->get_attachment_meta( $source_post_thumbnail_id )
			: false;

		if ( false === $source_post_thumbnail_meta ) {
			$this->logger->log_success( 'post-thumbnail/missing-meta', $source_post_thumbnail_id );

			return $data;
		}

		$source_upload_dir = wp_upload_dir();

		switch_to_blog( $this->import_coordinates->dest_blog_id );

		if ( $source_post_thumbnail_attachment instanceof \WP_Post ) {
			// in some instances the folder sep. `/` might be duplicated, we de-duplicate it
			array_walk(
				$source_upload_dir,
				function ( &$entry ) {
					$entry = str_replace( '//', '/', $entry );
				}
			);
			$source_uploads_dir         = untrailingslashit(
				str_replace(
					$source_upload_dir['subdir'],
					'',
					$source_upload_dir['path']
				)
			);
			$source_post_thumbnail_file = $source_uploads_dir . '/' . $source_post_thumbnail_meta['_wp_attached_file'];

			// Check the type of file. We'll use this as the 'post_mime_type'.
			$filetype = wp_check_filetype( basename( $source_post_thumbnail_file ), null );

			// Prepare an array of post data for the attachment.
			$attachment = array(
				'guid'           => $source_post_thumbnail_attachment->guid,
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $source_post_thumbnail_file ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			$existing_criteria = array(
				'post_type' => 'attachment',
				'title'     => $attachment['post_title'],
			);

			$found = get_posts( $existing_criteria );

			if ( $found && $found[0] instanceof \WP_Post ) {
				$dest_post_thumbnail_id = $found[0]->ID;
				$this->logger->log_success( 'post-thumbnail/existing', $dest_post_thumbnail_id );
			} else {
				// Insert the attachment.
				$dest_post_thumbnail_id = wp_insert_attachment(
					$attachment,
					$source_post_thumbnail_file,
					$dest_post_id
				);

				if ( empty( $dest_post_thumbnail_id ) ) {
					$this->logger->log_error( 'post-thumbnail/created', $dest_post_thumbnail_id );
				} else {
					$this->logger->log_success( 'post-thumbnail/created', $dest_post_thumbnail_id );
				}

				// the `_wp_attached_file` meta has been set before, so we skip it
				unset( $source_post_thumbnail_meta['_wp_attached_file'] );

				foreach ( $source_post_thumbnail_meta as $key => $value ) {
					add_post_meta( $dest_post_thumbnail_id, $key, $value, true );
				}
			}

			update_post_meta(
				$dest_post_thumbnail_id,
				AttachmentPathFinder::LINKED,
				array(
					'blog' => $source_blog_id,
					'post' => $source_post_thumbnail_id,
				)
			);

			$dest_post_thumbnail_set = set_post_thumbnail( $dest_post_id, $dest_post_thumbnail_id );

			if ( $dest_post_thumbnail_set || $found ) {
				$this->logger->log_success( 'post-thumbnail/set', $dest_post_thumbnail_id );
			} else {
				$this->logger->log_error( 'post-thumbnail/set', $dest_post_thumbnail_id );
			}
		}

		restore_current_blog();

		return $data;
	}

	/**
	 * @param int $source_post_thumbnail_id
	 *
	 * @return array<string, mixed>
	 */
	protected function get_attachment_meta( $source_post_thumbnail_id ) {
		$keys = array( '_wp_attached_file', '_wp_attachment_metadata', '_wp_attachment_image_alt' );

		return array_combine(
			$keys,
			array_map(
				function ( $key ) use ( $source_post_thumbnail_id ) {
					return get_post_meta( $source_post_thumbnail_id, $key, true );
				},
				$keys
			)
		);
	}
}
