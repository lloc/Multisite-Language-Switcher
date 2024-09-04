<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\LogWriters\AdminNoticeLogger;
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
	public function register(): bool {
		if ( ! msls_options()->activate_content_import ) {
			return false;
		}

		$this->hook();

		return true;
	}

	/**
	 * Hooks the filters and actions for this service provider.
	 *
	 * Differently from the `register` method this method will not check for options to hook.
	 */
	public function hook(): void {
		add_action(
			'load-post.php',
			function () {
				ContentImporter::instance()->handle_import();
			}
		);
		add_action(
			'load-post.php',
			function () {
				add_action(
					'admin_notices',
					function () {
						AdminNoticeLogger::instance()->show_last_log();
					}
				);
			}
		);
		add_action(
			'load-post-new.php',
			function () {
				ContentImporter::instance()->handle_import();
			}
		);
		add_filter(
			'wp_insert_post_empty_content',
			function ( $empty ) {
				return ContentImporter::instance()->filter_empty( $empty );
			}
		);
		add_filter(
			'wp_get_attachment_url',
			function ( $url, $post_id ) {
				return AttachmentPathFinder::instance()->filter_attachment_url( $url, $post_id );
			},
			99,
			2
		);
		add_filter(
			'wp_calculate_image_srcset',
			function ( $sources, $sizeArray, $imageSrc, $imageMeta, $attachmentId ) {
				return AttachmentPathFinder::instance()->filter_srcset(
					$sources,
					$sizeArray,
					$imageSrc,
					$imageMeta,
					$attachmentId
				);
			},
			99,
			5
		);
	}
}
