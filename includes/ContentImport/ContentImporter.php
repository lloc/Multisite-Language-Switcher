<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\Importers\AttachmentsImporters;
use lloc\Msls\ContentImport\Importers\Importer;
use lloc\Msls\ContentImport\Importers\PostFieldsImporters;
use lloc\Msls\ContentImport\Importers\PostMetaImporters;
use lloc\Msls\ContentImport\Importers\PostThumbnailImporters;
use lloc\Msls\ContentImport\Importers\TermsImporters;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsOptionsPost;
use lloc\Msls\MslsRegistryInstance;

/**
 * Class ContentImporter
 *
 * Handles the request for a content import.
 *
 * @package lloc\Msls\ContentImport
 */
class ContentImporter extends MslsRegistryInstance {

	/**
	 * @var MslsMain
	 */
	protected $main;

	/**
	 * @var ImportLogger
	 */
	protected $logger;
	/**
	 * @var Relations
	 */
	protected $relations;

	/**
	 * ContentImporter constructor.
	 *
	 * @param \lloc\Msls\MslsMain|null $main
	 */
	public function __construct( MslsMain $main = null ) {
		$this->main = $main ?: MslsMain::init();
	}

	/**
	 * @return \lloc\Msls\ContentImport\ImportLogger
	 */
	public function get_logger() {
		return $this->logger;
	}

	/**
	 * @param \lloc\Msls\ContentImport\ImportLogger $logger
	 */
	public function set_logger( $logger ) {
		$this->logger = $logger;
	}

	/**
	 * @return \lloc\Msls\ContentImport\Relations
	 */
	public function get_relations() {
		return $this->relations;
	}

	/**
	 * @param \lloc\Msls\ContentImport\Relations $relations
	 */
	public function set_relations( $relations ) {
		$this->relations = $relations;
	}

	/**
	 * Filters the `wp_insert_post_data` filter to modify the data that will be inserted for
	 * a post and run the real import if needed.
	 *
	 * @param array $data
	 *
	 * @return array The updated, if needed, data array.
	 */
	public function on_wp_insert_post( array $data ) {
		if ( ! $this->pre_flight_check() || false === $sources = $this->parse_sources() ) {
			return $data;
		}

		list( $source_blog_id, $source_post_id ) = $sources;

		if ( $source_blog_id === get_current_blog_id() ) {
			return $data;
		}

		$source_lang  = MslsBlogCollection::get_blog_language( $source_blog_id );
		$dest_post_id = get_the_ID();
		$dest_blog_id = get_current_blog_id();
		$dest_lang    = MslsBlogCollection::get_blog_language( get_current_blog_id() );

		if ( empty( $dest_post_id ) ) {
			return $data;
		}

		switch_to_blog( $source_blog_id );
		$source_post = get_post( $source_post_id );
		restore_current_blog();

		if ( ! $source_post instanceof \WP_Post ) {
			return $data;
		}

		$import_coordinates = new ImportCoordinates();

		$import_coordinates->source_blog_id = $source_blog_id;
		$import_coordinates->source_post_id = $source_post_id;
		$import_coordinates->dest_blog_id   = $dest_blog_id;
		$import_coordinates->dest_post_id   = $dest_post_id;
		$import_coordinates->source_post    = $source_post;
		$import_coordinates->source_lang    = $source_lang;
		$import_coordinates->dest_lang      = $dest_lang;

		return $this->import_content( $import_coordinates, $data );
	}

	/**
	 * Whether the importer should run or not.
	 *
	 * @return bool
	 */
	protected function pre_flight_check() {
		if ( ! $this->main->verify_nonce() ) {
			return false;
		}

		if ( ! isset( $_POST['msls_import'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Parses the source blog and post IDs from the $_POST array validating them.
	 *
	 * @return array|bool
	 */
	public function parse_sources() {
		if ( ! isset( $_POST['msls_import'] ) ) {
			return false;
		}

		$import_data = array_filter( explode( '|', trim( $_POST['msls_import'] ) ), 'is_numeric' );

		if ( count( $import_data ) !== 2 ) {
			return false;
		}

		return array_map( 'intval', $import_data );
	}

	/**
	 * Imports content according to the provided coordinates.
	 *
	 * @param       ImportCoordinates $import_coordinates
	 * @param array                   $post_fields An optional array of post fields; this can be
	 *                                             left empty if the method is not called as a consequence
	 *                                             of filtering the `wp_insert_post_data` filter.
	 *
	 * @return array An array of modified post fields.
	 */
	public function import_content( ImportCoordinates $import_coordinates, array $post_fields = [] ) {
		if ( ! $import_coordinates->validate() ) {
			return $post_fields;
		}

		/**
		 * Fires before the import runs.
		 *
		 * @since TBD
		 *
		 * @param ImportCoordinates $import_coordinates
		 */
		do_action( 'msls_content_import_before_import', $import_coordinates );

		/**
		 * Filters the data before the import runs.
		 *
		 * @since TBD
		 *
		 * @param array             $post_fields
		 * @param ImportCoordinates $import_coordinates
		 */
		$post_fields = apply_filters( 'msls_content_import_data_before_import', $post_fields, $import_coordinates );

		/**
		 * Filters the importers map before it's populated.
		 *
		 * Returning a non `null` value here will override the creation of the importers map completely
		 * and use the one returned in the filter.
		 *
		 * @since TBD
		 *
		 * @param null              $importers
		 * @param ImportCoordinates $import_coordinates
		 */
		$importers = apply_filters( 'msls_content_import_importers', null, $import_coordinates );
		if ( null === $importers ) {
			$importers = [
				'post-fields'    => PostFieldsImporters::make( $import_coordinates ),
				'post-meta'      => PostMetaImporters::make( $import_coordinates ),
				'terms'          => TermsImporters::make( $import_coordinates ),
				'post-thumbnail' => PostThumbnailImporters::make( $import_coordinates ),
				'attachments'    => AttachmentsImporters::make( $import_coordinates ),
			];
		}

		/**
		 * Filters the map of importers that should be used.
		 *
		 * @since TBD
		 *
		 * @param array             $importers An array of importers in the shape [ <type> => <Importer $importer> ]
		 * @param ImportCoordinates $import_coordinates
		 */
		$importers = apply_filters( 'msls_content_import_importers_map', $importers, $import_coordinates );

		$log            = $this->logger ?: new ImportLogger( $import_coordinates );
		$relations      = $this->relations ?: new Relations( $import_coordinates );

		if ( ! empty( $importers ) && is_array( $importers ) ) {
			$source_post_id = $import_coordinates->source_post_id;
			$dest_lang      = $import_coordinates->dest_lang;
			$dest_post_id   = $import_coordinates->dest_post_id;
			$relations->should_create( MslsOptionsPost::create( $source_post_id ), $dest_lang, $dest_post_id );
			foreach ( $importers as $key => $importer ) {
				/** @var Importer $importer */
				$post_fields = $importer->import( $post_fields );
				$log->merge( $importer->get_logger() );
				$relations->merge( $importer->get_relations() );
			}
			$relations->create();
			$log->save();
		}

		/**
		 * Fires after the import ran.
		 *
		 * @since TBD
		 *
		 * @param ImportCoordinates $import_coordinates
		 * @param ImportLogger      $log
		 * @param Relations         $relations
		 */
		do_action( 'msls_content_import_after_import', $import_coordinates, $log, $relations );

		/**
		 * Filters the data after the import ran.
		 *
		 * @since TBD
		 *
		 * @param array             $post_fields
		 * @param ImportCoordinates $import_coordinates
		 * @param ImportLogger      $log
		 * @param Relations         $relations
		 */
		return apply_filters( 'msls_content_import_data_after_import', $post_fields, $import_coordinates, $log, $relations );
	}

	/**
	 * Filters whether the post should be considered empty or not.
	 *
	 * Empty posts would not be saved to database but it's fine if in
	 * the context of a content import as it will be populated.
	 *
	 * @param bool $empty
	 *
	 * @return bool
	 */
	public function filter_empty( $empty ) {
		if ( ! $this->main->verify_nonce() ) {
			return $empty;
		}

		if ( ! isset( $_POST['msls_import'] ) ) {
			return $empty;
		}

		return false;
	}
}