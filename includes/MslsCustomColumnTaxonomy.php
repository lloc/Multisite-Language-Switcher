<?php
/**
 * MslsCustomColumnTaxonomy
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Handling of existing/not existing translations in the backend 
 * listings of various taxonomies
 *
 * @package Msls
 */
class MslsCustomColumnTaxonomy extends MslsCustomColumn implements HookInterface {

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @return HookInterface
	 */
	public static function init(): HookInterface {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();
		$obj        = new self( $options, $collection );

		if ( ! $options->is_excluded() ) {
			$taxonomy = MslsTaxonomy::instance()->get_request();

			if ( ! empty( $taxonomy ) ) {
				add_filter( "manage_edit-{$taxonomy}_columns" , [ $obj, 'th' ] );
				add_action( "manage_{$taxonomy}_custom_column" , [ $obj, 'column_default' ], -100, 3 );
				add_action( "delete_{$taxonomy}", [ $obj, 'delete' ] );
			}
		}

		return $obj;
	}

	/**
	 * Table body
	 *
	 * @param string $deprecated
	 * @param string $column_name
	 * @param int $item_id
	 *
	 * @return void
	 */
	public function column_default( string $deprecated, string $column_name, int $item_id ): void {
		$this->td( $column_name, $item_id );
	}

	/**
	 * Delete
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $object_id
	 *
	 * @return void
	 */
	public function delete( $object_id ): void {
		$this->save( $object_id, MslsOptionsTax::class );
	}

}
