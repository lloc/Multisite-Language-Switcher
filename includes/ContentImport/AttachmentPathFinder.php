<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsRegistryInstance;

class AttachmentPathFinder extends MslsRegistryInstance {

	const LINKED = '_msls_linked';

	/**
	 * @param array<string, array<string, mixed>> $sources
	 * @param mixed                               $size_array
	 * @param string                              $image_src
	 * @param mixed                               $image_meta
	 * @param int                                 $attachment_id
	 *
	 * @return array<string, mixed>
	 */
	public function filter_srcset( array $sources, $size_array, $image_src, $image_meta, $attachment_id ): array {
		$msls_imported = $this->has_import_data( $attachment_id );
		if ( ! $msls_imported ) {
			return $sources;
		}

		$source_post = get_blog_post( $msls_imported['blog'], $msls_imported['post'] );
		if ( is_null( $source_post ) ) {
			return $sources;
		}

		$extension             = '.' . pathinfo( $source_post->guid, PATHINFO_EXTENSION );
		$pattern               = '/(-[\\d]+x[\\d]+)*' . preg_quote( $extension, '/' ) . '$/';
		$src_without_extension = preg_replace( $pattern, '', $image_src );

		foreach ( $sources as $key => &$value ) {
			preg_match( $pattern, $value['url'], $matches );
			$w_and_h      = ! empty( $matches[1] ) ? $matches[1] : '';
			$value['url'] = $src_without_extension . $w_and_h . $extension;
		}

		return $sources;
	}

	/**
	 * @param int $attachment_id
	 * @return array<string, mixed>|false
	 */
	protected function has_import_data( $attachment_id ) {
		if ( empty( $attachment_id ) ) {
			return false;
		}

		$msls_imported = get_post_meta( $attachment_id, self::LINKED, true );

		if ( ! (
			is_array( $msls_imported )
			&& array_key_exists( 'blog', $msls_imported )
			&& array_key_exists( 'post', $msls_imported )
		)
		) {
			delete_post_meta( $attachment_id, self::LINKED );

			return false;
		}

		return $msls_imported;
	}

	/**
	 * @param string $url
	 * @param int    $attachment_id
	 * @return string
	 */
	public function filter_attachment_url( $url, $attachment_id ) {
		$msls_imported = $this->has_import_data( $attachment_id );
		if ( ! $msls_imported ) {
			return $url;
		}

		$source_post = $this->get_source_post( $attachment_id, $msls_imported );

		if ( false === $source_post ) {
			return $url;
		}

		return $source_post->guid;
	}

	/**
	 * @param int                  $attachment_id
	 * @param array<string, mixed> $msls_imported
	 *
	 * @return \WP_Post|false
	 */
	protected function get_source_post( $attachment_id, $msls_imported ) {
		$source_post = get_blog_post( $msls_imported['blog'], $msls_imported['post'] );

		if ( ! $source_post instanceof \WP_Post ) {
			delete_post_meta( $attachment_id, self::LINKED );

			return false;
		}

		return $source_post;
	}
}
