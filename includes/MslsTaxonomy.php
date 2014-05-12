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

		$this->request = filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_SPECIAL_CHARS );
		if ( empty( $this->request ) ) {
			$this->request = get_query_var( 'taxonomy' );
		}

		if ( filter_has_var( INPUT_GET, 'post_type' ) ) {
			$this->post_type = filter_input(
				INPUT_GET,
				'post_type',
				FILTER_SANITIZE_SPECIAL_CHARS
			);
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
	static function instance() {
		if ( ! ( $obj = MslsRegistry::get_object( 'MslsTaxonomy' ) ) ) {
			$obj = new self();
			MslsRegistry::set_object( 'MslsTaxonomy', $obj );
		}
		return $obj;
	}

}
