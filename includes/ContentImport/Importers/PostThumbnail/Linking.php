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

	public function import( array $data ) {
		$source_blog_id = $this->import_coordinates->source_blog_id;
		$source_post_id = $this->import_coordinates->source_post_id;
		$dest_post_id   = $this->import_coordinates->dest_post_id;

		switch_to_blog( $source_blog_id );

		$source_post_thumbnail_id         = (int) get_post_thumbnail_id( $source_post_id );
		$source_post_thumbnail_attachment = get_post( $source_post_thumbnail_id );
		$source_post_thumbnail_meta       = $source_post_thumbnail_attachment instanceof \WP_Post ?
			wp_get_attachment_metadata( $source_post_thumbnail_id )
			: false;
		$source_upload_dir                = wp_upload_dir();

		restore_current_blog();

		if ( empty( get_post_thumbnail_id( $dest_post_id ) ) && $source_post_thumbnail_attachment instanceof \WP_Post ) {
			$source_post_thumbnail_file = $source_upload_dir['basedir'] . '/' . $source_post_thumbnail_meta['file'];

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

			// Insert the attachment.
			$dest_post_thumbnail_id = wp_insert_attachment( $attachment, $source_post_thumbnail_file, $dest_post_id );

			if ( empty( $dest_post_thumbnail_id ) ) {
				$this->logger->log_error( 'post-thumbnail/created', $dest_post_thumbnail_id );
			} else {
				$this->logger->log_success( 'post-thumbnail/created', $dest_post_thumbnail_id );
			}

			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// Generate the metadata for the attachment, and update the database record.
			$dest_post_thumbnail_meta = wp_generate_attachment_metadata( $dest_post_thumbnail_id, $source_post_thumbnail_file );
			wp_update_attachment_metadata( $dest_post_thumbnail_id, $dest_post_thumbnail_meta );
			update_post_meta( $dest_post_thumbnail_id, AttachmentPathFinder::IMPORTED, [ 'blog' => $source_blog_id, 'post' => $source_post_thumbnail_id ] );

			$dest_post_thumbnail_set = set_post_thumbnail( $dest_post_id, $dest_post_thumbnail_id );

			if ( $dest_post_thumbnail_set ) {
				$this->logger->log_success( 'post-thumbnail/set', $dest_post_thumbnail_id );
			} else {
				$this->logger->log_error( 'post-thumbnail/set', $dest_post_thumbnail_id );
			}

			// @todo should dest post thumbnail be related with the source one?
		}

		return $data;
	}
}