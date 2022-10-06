<?php
/**
 * MslsTaxonomy
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Content types: Taxonomies (Tags, Categories, ...)
 *
 * @package Msls
 */
class MslsTaxonomy extends MslsContentTypes {

	/**
	 * Post type
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->types = self::get();
		$this->request = $this->get_request();
	}

	/**
	 * @uses get_post_types
	 * @return string[]
	 */
	public static function get(): array {
		$types = array_merge(
			[ 'category', 'post_tag' ], // no 'post_link' here
			get_taxonomies( [ 'public' => true, '_builtin' => false ] )
		);

		return (array) apply_filters( 'msls_supported_taxonomies', $types );
	}

	/**
	 * @return string
	 */
	public function get_request(): string {
		$request = MslsPlugin::get_superglobals( [ 'taxonomy', 'post_type' ] );

		if ( ! empty( $request['taxonomy'] ) ) {
			$this->post_type = esc_attr( $request['post_type'] ?? '' );

			return esc_attr( $request['taxonomy'] );
		}

		return get_query_var( 'taxonomy' );
	}

	/**
	 * Check for taxonomy
	 * @return bool
	 */
	public function is_taxonomy() {
		return true;
	}

	/**
	 * Check if the current user can manage this content type
	 *
	 * Returns name of the content type if the user has access or an empty
	 * string if the user can not access
	 *
	 * @return string
	 */
	public function acl_request() {
		if ( ! MslsOptions::instance()->is_excluded() ) {
			$request = $this->get_request();

			$tax = get_taxonomy( $request );
			if ( $tax && current_user_can( $tax->cap->manage_terms ) ) {
				return $request;
			}
		}

		return parent::acl_request();
	}

	/**
	 * Get the requested post_type of the taxonomy
	 *
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

}
