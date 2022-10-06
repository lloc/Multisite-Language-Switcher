<?php
/**
 * MslsPostType
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Content types: Post types (Pages, Posts, ...)
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
	 * @uses get_post_types
	 *
	 * @return string[]
	 */
	public static function get(): array {
		$types = array_merge(
			[ 'post', 'page' ], // we don't need attachment, revision or nav_menu_item here
			get_post_types( [ 'public' => true, '_builtin' => false ] )
		);

		return (array) apply_filters( 'msls_supported_post_types', $types );
	}

	/**
	 * @return string
	 */
	public function get_request(): string {
		$request   = MslsPlugin::get_superglobals( [ 'post_type' ] );
		$post_type = ! empty( $request['post_type'] ) ? esc_attr( $request['post_type'] ) : 'post';

		return in_array( $post_type, $this->get() ) ? $post_type : '';
	}

	/**
	 * Check for post_type
	 * @return bool
	 */
	public function is_post_type() {
		return true;
	}

}
