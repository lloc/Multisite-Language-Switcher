<?php
/**
 * MslsTaxonomy
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Content types: Taxonomies (Tags, Categories, ...)
 * @package Msls
 */
class MslsTaxonomy extends MslsContentTypes implements IMslsRegistryInstance {

	/**
	 * Post type
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->types = array_merge(
			array( 'category', 'post_tag' ), // no 'post_link' here
			get_taxonomies(
				array( 'public' => true, '_builtin' => false ),
				'names',
				'and'
			)
		);

		$_request = MslsPlugin::get_superglobals( array( 'taxonomy', 'post_type' ) );
		if ( '' != $_request['taxonomy'] ) {
			$this->request   = esc_attr( $_request['taxonomy'] );
			$this->post_type = esc_attr( $_request['post_type'] );
		}
		else {
			$this->request = get_query_var( 'taxonomy' );
		}
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
		return '';
	}

	/**
	 * Get the requested post_type of the taxonomy
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Get or create an instance of MslsTaxonomy
	 * @todo Until PHP 5.2 is not longer the minimum for WordPress ...
	 * @return MslsTaxonomy
	 */
	public static function instance() {
		if ( ! ( $obj = MslsRegistry::get_object( 'MslsTaxonomy' ) ) ) {
			$obj = new self();
			MslsRegistry::set_object( 'MslsTaxonomy', $obj );
		}
		return $obj;
	}

}
