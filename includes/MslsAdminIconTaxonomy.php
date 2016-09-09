<?php
/**
 * MslsAdminIconTaxonomy
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Handles backend icons for taxonomies
 * @package Msls
 */
class MslsAdminIconTaxonomy extends MslsAdminIcon {

	/**
	 * Path
	 * @var string
	 */
	protected $path = 'edit-tags.php';

	/**
	 * Set href
	 * @uses get_edit_term_link() 
	 * @param int $id
	 * @return MslsAdminIconTaxonomy
	 */
	public function set_href( $id ) {
		$this->href = get_edit_term_link(
			$id,
			$this->type,
			MslsTaxonomy::instance()->get_post_type()
		);
		return $this;
	}

	/**
	 * Set the path by type
	 * @uses add_query_arg()
	 * @return MslsAdminIconTaxonomy
	 */
	public function set_path() {
		$args      = array( 'taxonomy' => $this->type );
		$post_type = MslsTaxonomy::instance()->get_post_type();
		if ( ! empty( $post_type ) ) {
			$args['post_type'] = $post_type;
		}
		$this->path = add_query_arg( $args, $this->path );
		return $this;
	}

}
