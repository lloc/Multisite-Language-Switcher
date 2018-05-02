<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsBlogCollection;

class ImportCoordinates {

	const IMPORTERS_GLOBAL_KEY = 'msls_importers';

	/**
	 * @var int
	 */
	public $source_blog_id;
	/**
	 * @var int
	 */
	public $source_post_id;
	/**
	 * @var int
	 */
	public $dest_blog_id;
	/**
	 * @var int
	 */
	public $dest_post_id;

	/**
	 * @var \WP_Post
	 */
	public $source_post;

	/**
	 * @var string
	 */
	public $source_lang;

	/**
	 * @var string
	 */
	public $dest_lang;

	/**
	 * @var array An array keeping track of which importer (slug) should be used for
	 *            a specific import type, shape [ <import-type> => <slug> ]
	 */
	public $importers = [];

	/**
	 * Validates the coordinates.
	 *
	 * @return bool
	 */
	public function validate() {
		if ( ! get_blog_post( $this->source_blog_id, $this->source_post_id ) ) {
			return false;
		}
		if ( ! get_blog_post( $this->dest_blog_id, $this->dest_post_id ) ) {
			return false;
		}
		if ( ! $this->source_post instanceof \WP_Post ) {
			return false;
		}

		if ( $this->source_lang !== MslsBlogCollection::get_blog_language( $this->source_blog_id ) ) {
			return false;
		}
		if ( $this->dest_lang !== MslsBlogCollection::get_blog_language( $this->dest_blog_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the importer (slug) for a specific type of imports.
	 *
	 * @param string $importer_type
	 *
	 * @return string|null The import slug if set or `null` if not set.
	 */
	public function get_importer_for( $importer_type ) {
		return isset( $this->importers[ $importer_type ] )
			? $this->importers[ $importer_type ]
			: null;
	}

	/**
	 * Parses the importers from request superglobals.
	 */
	public function parse_importers_from_request() {
		// bitmask
		$source = isset( $_REQUEST[ self::IMPORTERS_GLOBAL_KEY ] ) * 100
		          + isset( $_POST[ self::IMPORTERS_GLOBAL_KEY ] ) * 10
		          + isset( $_GET[ self::IMPORTERS_GLOBAL_KEY ] );

		switch ( $source ) {
			case 100:
			case 101;
			case 111:
			case 110:
				$source = $_REQUEST;
				break;
			case 010:
			case 011:
			case 000:
			default:
				$source = $_POST;
				break;
			case 001:
				$source = $_GET;
				break;
		}

		$importers = isset( $source[ self::IMPORTERS_GLOBAL_KEY ] ) && is_array( $source[ self::IMPORTERS_GLOBAL_KEY ] )
			? $source[ self::IMPORTERS_GLOBAL_KEY ]
			: [];

		foreach ( $importers as $importer_type => $slug ) {
			$this->set_importer_for( $importer_type, $slug );
		}
	}

	/**
	 * Sets the slug of the importer that should be used for a type of import.
	 *
	 * @param string $importer_type
	 * @param string $slug
	 */
	public function set_importer_for( $importer_type, $slug ) {
		$this->importers[ $importer_type ] = $slug;
	}
}
