<?php declare( strict_types=1 );

namespace lloc\Msls\Admin;

use lloc\Msls\ContentTypes\Taxonomy;

/**
 * Handles backend icons for taxonomies
 *
 * @package Msls
 */
class IconTaxonomy extends Icon {

	protected string $path = 'edit-tags.php';

	/**
	 * Set href
	 *
	 * @param int $id
	 *
	 * @return IconTaxonomy
	 * @uses get_edit_term_link()
	 */
	public function set_href( int $id ): Icon {
		$object_type = Taxonomy::instance()->get_post_type();

		$this->href = get_edit_term_link( $id, $this->type, $object_type ) ?? '';

		return $this;
	}

	/**
	 * Set the path by type
	 *
	 * @return IconTaxonomy
	 */
	public function set_path(): Icon {
		$args = array( 'taxonomy' => $this->type );

		$post_type = Taxonomy::instance()->get_post_type();
		if ( '' !== $post_type ) {
			$args['post_type'] = $post_type;
		}

		$this->path = add_query_arg( $args, $this->path );

		return $this;
	}
}
