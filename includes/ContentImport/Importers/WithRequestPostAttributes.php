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

use lloc\Msls\MslsRequest;

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
	 * @param string $default The default post type to return if none is specified in the `$_REQUEST` super-global.
	 *
	 * @return string Either the post type read from the `$_REQUEST` super-global, or the default value.
\    *
	 */
	protected function read_post_type_from_request( $default = 'post' ) {
		$request = MslsRequest::get_request( array( 'post_type' ), $default );

		return $request['post_type'];
	}
}
