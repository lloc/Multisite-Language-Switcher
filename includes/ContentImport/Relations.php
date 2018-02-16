<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsOptions;

/**
 * Class Relations
 *
 * Manages and tracks the relations between elements managed by MSLS that are created in the context of an import.
 *
 * @package lloc\Msls\ContentImport
 */
class Relations {

	/**
	 * @var array
	 */
	public $to_create = [];
	/**
	 * @var ImportCoordinates
	 */
	protected $import_coordinates;

	/**
	 * Relations constructor.
	 *
	 * @param ImportCoordinates $import_coordinates
	 */
	public function __construct( ImportCoordinates $import_coordinates ) {
		$this->import_coordinates = $import_coordinates;
	}

	/**
	 * Merges the data from a Relations object into this one.
	 *
	 * @param Relations|null $relations
	 */
	public function merge( Relations $relations = null ) {
		if ( null === $relations ) {
			return;
		}

		$this->to_create = array_merge_recursive( $this->to_create, $relations->get_data() );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return $this->to_create;
	}

	/**
	 * Creates the relations between the source blog elements and the destination one.
	 */
	public function create() {
		switch_to_blog( $this->import_coordinates->source_blog_id );
		foreach ( $this->to_create as $relation ) {
			/** @var MslsOptions $option */
			list( $option, $lang, $id ) = $relation;
			$option->save( [ $lang => $id ] );
		}
		restore_current_blog();
	}

	/**
	 * Sets a relation that should be created.
	 *
	 * @param MslsOptions $creator
	 * @param string      $dest_lang
	 * @param string      $dest_post_id
	 */
	public function should_create( MslsOptions $creator, $dest_lang, $dest_post_id ) {
		$this->to_create[] = [ $creator, $dest_lang, $dest_post_id ];
	}
}