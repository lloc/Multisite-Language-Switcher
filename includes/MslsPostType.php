<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * Content types: Post types (Pages, Posts, ...)
 *
 * @package Msls
 */
class MslsPostType extends MslsContentTypes {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->types   = self::get();
		$this->request = $this->get_request();
	}

	/**
	 * @return string[]
	 * @uses get_post_types
	 */
	public static function get(): array {
		$types = array_merge(
			array( 'post', 'page' ), // we don't need attachment, revision or nav_menu_item here
			get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				)
			)
		);

		return (array) apply_filters( 'msls_supported_post_types', $types );
	}

	/**
	 * @return string
	 */
	public function get_request(): string {
		$request   = MslsRequest::get_request( array( 'post_type' ) );
		$post_type = ! empty( $request['post_type'] ) ? esc_attr( $request['post_type'] ) : 'post';

		return in_array( $post_type, $this->get() ) ? $post_type : '';
	}

	/**
	 * Check for post_type
	 *
	 * @return bool
	 */
	public function is_post_type() {
		return true;
	}
}
