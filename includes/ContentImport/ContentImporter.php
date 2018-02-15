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

	public function __construct( MslsMain $main = null ) {
		$this->main = null !== $main ?: MslsMain::init();
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
		 * @param array             $data
		 * @param ImportCoordinates $import_coordinates
		 */
		$data = apply_filters( 'msls_content_import_data_before_import', $data, $import_coordinates );

		$importers = [
			'post-fields'    => PostFieldsImporters::make( $import_coordinates ),
			'post-meta'      => PostMetaImporters::make( $import_coordinates ),
			'terms'          => TermsImporters::make( $import_coordinates ),
			'post-thumbnail' => PostThumbnailImporters::make( $import_coordinates ),
			'attachments'    => AttachmentsImporters::make( $import_coordinates ),
		];

		/**
		 * Filters the map of importers that should be used.
		 *
		 * @since TBD
		 *
		 * @param array             $importers An array of importers in the shape [ <type> => <Importer $importer> ]
		 * @param ImportCoordinates $import_coordinates
		 */
		$importers = apply_filters( 'msls_content_import_importers', $importers, $import_coordinates );

		$log       = isset( $this->logger ) ?: new ImportLogger( $import_coordinates );
		$relations = isset( $this->relations ) ?: new Relations( $import_coordinates );

		$relations->should_create( MslsOptionsPost::create( $source_post_id ), $dest_lang, $dest_post_id );

		foreach ( $importers as $key => $importer ) {
			/** @var Importer $importer */
			$data = $importer->import( $data );
			$log->merge( $importer->get_logger() );
			$relations->merge( $importer->get_relations() );
		}

		$relations->create();
		$log->save();

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
		 * @param array             $data
		 * @param ImportCoordinates $import_coordinates
		 * @param ImportLogger      $log
		 * @param Relations         $relations
		 */
		return apply_filters( 'msls_content_import_data_after_import', $data, $import_coordinates, $log, $relations );
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

	protected function parse_sources() {
		$import_data = array_filter( explode( '|', trim( $_POST['msls_import'] ) ), 'is_numeric' );

		if ( count( $import_data ) !== 2 ) {
			return false;
		}

		return array_map( 'intval', $import_data );
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