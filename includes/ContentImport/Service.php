<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsOptions;
use lloc\Msls\MslsRegistryInstance;

/**
 * Class Service
 *
 * A service provider for the content import functionality.
 *
 * @package lloc\Msls\ContentImport
 */
class Service extends MslsRegistryInstance {

	/**
	 * Hooks the classes that provide the content import functionality if the content import option is active.
	 *
	 * @return bool Whether the content import functionality support classes where hooked or not.
	 */
	public function register() {
		if ( ! MslsOptions::instance()->activate_content_import ) {
			return false;
		}

		add_filter( 'wp_insert_post_data', function ( array $data ) {
			ContentImporter::instance()->import( $data );
		}, 99 );
		add_filter( 'wp_insert_post_empty_content', function ( $empty ) {
			ContentImporter::instance()->filter_empty( $empty );
		} );
		add_filter( 'wp_get_attachment_image_src', function ( $image, $attachmentId ) {
			AttachmentPathFinder::instance()->filter_src( $image, $attachmentId );
		}, 99, 2 );
		add_filter( 'wp_calculate_image_srcset', function ( $sources, $sizeArray, $imageSrc, $imageMeta, $attachmentId ) {
			AttachmentPathFinder::instance()->filter_srcset( $sources, $sizeArray, $imageSrc, $imageMeta, $attachmentId );
		}, 99, 5 );

		return true;
	}
}