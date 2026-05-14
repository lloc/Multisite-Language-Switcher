<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\ContentTypes\Taxonomy;

/**
 * Handles backend icons for taxonomies
 *
 * @package Msls
 */
class MslsAdminIconTaxonomy extends MslsAdminIcon {

	protected string $path = 'edit-tags.php';

	/**
	 * Set href
	 *
	 * @param int $id
	 *
	 * @return MslsAdminIconTaxonomy
	 * @uses get_edit_term_link()
	 */
	public function set_href( int $id ): MslsAdminIcon {
		$object_type = Taxonomy::instance()->get_post_type();

		$this->href = get_edit_term_link( $id, $this->type, $object_type ) ?? '';

		return $this;
	}

	/**
	 * Set the path by type
	 *
	 * @return MslsAdminIconTaxonomy
	 */
	public function set_path(): MslsAdminIcon {
		$args = array( 'taxonomy' => $this->type );

		$post_type = Taxonomy::instance()->get_post_type();
		if ( '' !== $post_type ) {
			$args['post_type'] = $post_type;
		}

		$this->path = add_query_arg( $args, $this->path );

		return $this;
	}
}
