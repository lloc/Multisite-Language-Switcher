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
	 * @var MslsOptions[]
	 */
	public $to_create = [];

	/**
	 * @var MslsOptions[]
	 */
	protected $local_options = [];

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
		$this->create_source_to_local();
		$this->create_local_to_source();
		restore_current_blog();
	}

	protected function create_source_to_local() {
		switch_to_blog( $this->import_coordinates->source_blog_id );

		foreach ( $this->to_create as $relation ) {
			/** @var MslsOptions $option */
			list( $option, $lang, $id ) = $relation;
			$option->save( [ $lang => $id ] );
			$source_id = $option->get_arg( 0, $id );

			$this->local_options[ $source_id ] = [ $option, $id ];
		}
	}

	protected function create_local_to_source() {
		switch_to_blog( $this->import_coordinates->dest_blog_id );

		/** @var MslsOptions $local_option */
		foreach ( $this->local_options as $source_id => $option_data ) {
			list( $source_option, $local_id ) = $option_data;

			/**
			 * Allows plugins to filter the local to source relation creation and override the class creation method completely.
			 *
			 * @param mixed $created If not `null` then the class will not create the local to source relation.
			 * @param int $local_id
			 * @param int $source_id
			 * @param MslsOptions $source_option
			 */
			$created = apply_filters( 'msls_content_import_relation_local_to_source_create', null, $local_id, $source_id, $source_option );
			if ( null !== $created ) {
				continue;
			}

			$option_class = get_class( $source_option );
			$local_option = call_user_func( [ $option_class, 'create' ], $local_id );
			$local_option->save( [ $this->import_coordinates->source_lang => $source_id ] );
		}
	}

	/**
	 * Sets a relation that should be created.
	 *
	 * @param MslsOptions $creator
	 * @param string $dest_lang
	 * @param string $dest_post_id
	 */
	public function should_create( MslsOptions $creator, $dest_lang, $dest_post_id ) {
		$this->to_create[] = [ $creator, $dest_lang, $dest_post_id ];
	}
}