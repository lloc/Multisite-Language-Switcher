<?php
/**
 * Provides methods to parse and get information about the post object of the import from
 * the request parameters.
 *
 * @since   TBD
 *
 * @package lloc\Msls\ContentImport\Importers
 */

namespace lloc\Msls\ContentImport\Importers;

/**
 * Trait WithRequestPostAttributes
 *
 * @since   TBD
 *
 * @package lloc\Msls\ContentImport\Importers
 */
trait WithRequestPostAttributes {
	/**
	 * Returns the post type read from `$_REQUEST['post_type']` if any, or a default post type.
	 *
	 * @since TBD
	 *
	 * @param string $default The default post type to return if none is specified in the `$_REQUEST` super-global.
	 *
	 * @return string Either the post type read from the `$_REQUEST` super-global, or the default value.
	 */
	protected function read_post_type_from_request( $default = 'post' ) {
		if ( ! isset( $_REQUEST['post_type'] ) ) {
			return $default;
		}

		return filter_var( $_REQUEST['post_type'], FILTER_SANITIZE_STRING ) ?: 'post';
	}
}
