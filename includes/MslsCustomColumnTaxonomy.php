<?php

namespace lloc\Msls;

/**
 * Handling of existing/not existing translations in the backend
 * listings of various taxonomies
 *
 * @package Msls
 */
final class MslsCustomColumnTaxonomy extends MslsCustomColumn {

	protected function add_hooks(): void {
		if ( $this->options->is_excluded() ) {
			return;
		}

		$taxonomy = msls_taxonomy()->get_request();

		if ( ! empty( $taxonomy ) ) {
			add_filter( "manage_edit-{$taxonomy}_columns", array( $this, 'th' ) );
			add_action( "manage_{$taxonomy}_custom_column", array( $this, 'column_default' ), - 100, 3 );
			add_action( "delete_{$taxonomy}", array( $this, 'delete' ) );
		}
	}

	/**
	 * @param string $deprecated
	 * @param string $column_name
	 * @param int    $item_id
	 */
	public function column_default( $deprecated, $column_name, $item_id ): void {
		$this->td( $column_name, $item_id );
	}

	/**
	 * @codeCoverageIgnore
	 *
	 * @param int $object_id
	 */
	public function delete( $object_id ): void {
		$this->save( $object_id, MslsOptionsTax::class );
	}
}
