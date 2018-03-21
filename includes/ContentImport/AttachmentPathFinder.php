<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsRegistryInstance;

class AttachmentPathFinder extends MslsRegistryInstance {

	const IMPORTED = '_msls_imported';

	public function filter_src( array $image, $attachment_id ) {
		if ( empty( $image ) || false === ( $msls_imported = $this->has_import_data( $attachment_id ) ) ) {
			return $image;
		}

		$sourcePost = get_blog_post( $msls_imported['blog'], $msls_imported['post'] );

		if ( empty( $sourcePost ) ) {
			delete_post_meta( $attachment_id, self::IMPORTED );

			return $image;
		}

		$image[0] = $sourcePost->guid;

		return $image;
	}

	protected function has_import_data( $attachment_id ) {
		if ( empty( $attachment_id ) ) {
			return false;
		}

		$msls_imported = get_post_meta( $attachment_id, self::IMPORTED, true );

		if ( ! (
			is_array( $msls_imported )
			&& array_key_exists( 'blog', $msls_imported )
			&& array_key_exists( 'post', $msls_imported )
		)
		) {
			delete_post_meta( $attachment_id, self::IMPORTED );

			return false;
		}

		return $msls_imported;
	}

	public function filter_srcset( array $sources, $sizeArray, $imageSrc, $imageMeta, $attachmentId ) {
		if ( ! $this->has_import_data( $attachmentId ) ) {
			return $sources;
		}

		$extension              = '.' . pathinfo( $imageSrc, PATHINFO_EXTENSION );
		$srcSrcWithoutExtension = str_replace( $extension, '', $imageSrc );

		foreach ( $sources as $key => &$value ) {
			$srcWithoutExtension = str_replace( $extension, '', $value['url'] );
			$srcWithoutExtension = preg_replace( '/-[\\d]+x[\\d]+$/', '', $srcWithoutExtension );
			$value['url']        = str_replace( $srcWithoutExtension, $srcSrcWithoutExtension, $value['url'] );
		}

		return $sources;
	}
}