<?php

namespace lloc\Msls;

/**
 * Handling of existing/not existing translations in the backend
 * listings of various taxonomies
 *
 * @package Msls
 */
class MslsCustomColumnTaxonomy extends MslsCustomColumn {

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsCustomColumnTaxonomy
	 */
	public static function init() {
		$options    = msls_options();
		$collection = msls_blog_collection();
		$obj        = new static( $options, $collection );

		if ( ! $options->is_excluded() ) {
			$taxonomy = MslsTaxonomy::instance()->get_request();

			if ( ! empty( $taxonomy ) ) {
				add_filter( "manage_edit-{$taxonomy}_columns", array( $obj, 'th' ) );
				add_action( "manage_{$taxonomy}_custom_column", array( $obj, 'column_default' ), - 100, 3 );
				add_action( "delete_{$taxonomy}", array( $obj, 'delete' ) );
			}
		}

		return $obj;
	}

	/**
	 * Table body
	 *
	 * @param string $deprecated
	 * @param string $column_name
	 * @param int    $item_id
	 */
	public function column_default( $deprecated, $column_name, $item_id ) {
		$this->td( $column_name, $item_id );
	}

	/**
	 * Delete
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $object_id
	 */
	public function delete( $object_id ) {
		$this->save( $object_id, MslsOptionsTax::class );
	}
}
