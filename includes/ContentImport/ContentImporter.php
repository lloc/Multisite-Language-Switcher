<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\Importers\Importer;
use lloc\Msls\ContentImport\Importers\Map;
use lloc\Msls\ContentImport\Importers\WithRequestPostAttributes;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsOptionsPost;
use lloc\Msls\MslsRegistryInstance;
use lloc\Msls\MslsRequest;

/**
 * Class ContentImporter
 *
 * Handles the request for a content import.
 *
 * @package lloc\Msls\ContentImport
 */
class ContentImporter extends MslsRegistryInstance {
	use WithRequestPostAttributes;

	/**
	 * @var MslsMain
	 */
	protected MslsMain $main;

	/**
	 * @var ImportLogger|null
	 */
	protected ?ImportLogger $logger = null;

	/**
	 * @var Relations|null
	 */
	protected ?Relations $relations = null;

	/**
	 * @var bool Whether the class should handle requests or not.
	 */
	protected bool $handle = true;

	/**
	 * @var int The ID of the post the class created while handling the request, if any.
	 */
	protected int $has_created_post = 0;

	/**
	 * ContentImporter constructor.
	 *
	 * @param ?MslsMain $main
	 */
	public function __construct( ?MslsMain $main = null ) {
		$this->main = ! is_null( $main ) ? $main : MslsMain::create();
	}

	/**
	 * @return ?ImportLogger
	 */
	public function get_logger(): ?ImportLogger {
		return $this->logger;
	}

	/**
	 * @param ImportLogger $logger
	 */
	public function set_logger( ImportLogger $logger ): void {
		$this->logger = $logger;
	}

	/**
	 * @return ?Relations
	 */
	public function get_relations(): ?Relations {
		return $this->relations;
	}

	/**
	 * @param Relations $relations
	 */
	public function set_relations( Relations $relations ): void {
		$this->relations = $relations;
	}

	/**
	 * Handles an import request happening during a post save or a template redirect.
	 *
	 * @param string[] $data
	 *
	 * @return string[] The updated, if needed, data array.
	 */
	public function handle_import( array $data = array() ) {
		$sources = $this->parse_sources();
		if ( ! $this->pre_flight_check() || false === $sources ) {
			return $data;
		}

		list( $source_blog_id, $source_post_id ) = $sources;

		if ( get_current_blog_id() === $source_blog_id ) {
			return $data;
		}

		$source_lang  = MslsBlogCollection::get_blog_language( $source_blog_id );
		$dest_blog_id = get_current_blog_id();
		$dest_lang    = MslsBlogCollection::get_blog_language( get_current_blog_id() );

		$dest_post_id = $this->get_the_blog_post_ID( $dest_blog_id );

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

		$import_coordinates->parse_importers_from_request();

		$data = $this->import_content( $import_coordinates, $data );

		if ( $this->has_created_post ) {
			$this->update_inserted_blog_post_data( $dest_blog_id, $dest_post_id, $data );
			$this->redirect_to_blog_post( $dest_blog_id, $dest_post_id );
		}

		return $data;
	}

	/**
	 * Whether the importer should run or not.
	 *
	 * @return bool
	 */
	protected function pre_flight_check() {
		if ( ! $this->handle ) {
			return false;
		}

		if ( ! $this->main->verify_nonce() ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['msls_import'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Parses the source blog and post IDs from the $_POST array validating them.
	 *
	 * @return int[]|bool
	 */
	public function parse_sources() {
		if ( ! MslsRequest::has_var( 'msls_import' ) ) {
			return false;
		}

		$msls_import = MslsRequest::get_var( 'msls_import' );
		$import_data = array_filter( explode( '|', trim( $msls_import ) ), 'is_numeric' );

		if ( count( $import_data ) !== 2 ) {
			return false;
		}

		return array_map( 'intval', $import_data );
	}

	/**
	 * @param int $blog_id
	 *
	 * @return int
	 */
	protected function get_the_blog_post_ID( $blog_id ) {
		switch_to_blog( $blog_id );

		$id = get_the_ID();

		if ( ! empty( $id ) ) {
			restore_current_blog();

			return $id;
		}

		$request = MslsRequest::get_request( array( 'post' ) );
		if ( ! empty( $request['post'] ) ) {
			return (int) $request['post'];
		}

		$data = array(
			'post_type'  => $this->read_post_type_from_request( 'post' ),
			'post_title' => 'MSLS Content Import Draft - ' . ( new \DateTimeImmutable() )->format( 'Y-m-d H:i:s' ),
		);

		return $this->insert_blog_post( $blog_id, $data );
	}

	/**
	 * @param int                  $blog_id
	 * @param array<string, mixed> $data
	 *
	 * @return bool|int
	 */
	protected function insert_blog_post( $blog_id, array $data = array() ) {
		if ( empty( $data ) ) {
			return false;
		}

		switch_to_blog( $blog_id );

		$this->handle( false );
		if ( isset( $data['ID'] ) ) {
			$post_id = wp_update_post( $data );
		} else {
			$post_id = wp_insert_post( $data );
		}
		$this->handle( true );

		$this->has_created_post = $post_id > 0 ? $post_id : false;

		restore_current_blog();

		return $this->has_created_post;
	}

	/**
	 * @param bool $handle
	 *
	 * @return void
	 */
	public function handle( $handle ) {
		$this->handle = $handle;

		// Also, prevent MSLS from saving.
		if ( false === $handle ) {
			add_action( 'msls_main_save', 'msls_return_void' );
		} else {
			remove_action( 'msls_main_save', 'msls_return_void' );
		}
	}

	/**
	 * Imports content according to the provided coordinates.
	 *
	 * @param ImportCoordinates $import_coordinates
	 * @param string[]          $post_fields        An optional array of post fields; this can be
	 *                                              left empty if the method is not called as a consequence
	 *                                              of filtering the `wp_insert_post_data` filter.
	 *
	 * @return string[] An array of modified post fields.
	 */
	public function import_content( ImportCoordinates $import_coordinates, array $post_fields = array() ) {
		if ( ! $import_coordinates->validate() ) {
			return $post_fields;
		}

		/**
		 * Fires before the import runs.
		 *
		 * @param ImportCoordinates $import_coordinates
		 */
		do_action( 'msls_content_import_before_import', $import_coordinates );

		/**
		 * Filters the data before the import runs.
		 *
		 * @param array $post_fields
		 * @param ImportCoordinates $import_coordinates
		 *
		 * @since TBD
		 */
		$post_fields = apply_filters( 'msls_content_import_data_before_import', $post_fields, $import_coordinates );

		/**
		 * Filters the importers map before it's populated.
		 *
		 * Returning a non `null` value here will override the creation of the importers map completely
		 * and use the one returned in the filter.
		 *
		 * @param ?array $importers
		 * @param ImportCoordinates $import_coordinates
		 */
		$importers = apply_filters( 'msls_content_import_importers', null, $import_coordinates );

		if ( is_null( $importers ) ) {
			$importers = Map::instance()->make( $import_coordinates );
		}

		if ( is_null( $this->get_logger() ) ) {
			$this->set_logger( new ImportLogger( $import_coordinates ) );
		}

		if ( is_null( $this->get_relations() ) ) {
			$this->set_relations( new Relations( $import_coordinates ) );
		}

		if ( ! empty( $importers ) ) {
			$source_post_id = $import_coordinates->source_post_id;
			$dest_lang      = $import_coordinates->dest_lang;
			$dest_post_id   = $import_coordinates->dest_post_id;
			$this->relations->should_create( MslsOptionsPost::create( $source_post_id ), $dest_lang, $dest_post_id );

			foreach ( $importers as $key => $importer ) {
				/** @var Importer $importer */
				$post_fields = $importer->import( $post_fields );
				$this->logger->merge( $importer->get_logger() );
				$this->relations->merge( $importer->get_relations() );
			}

			$this->relations->create();
			$this->logger->save();
		}

		/**
		 * Fires after the import ran.
		 *
		 * @param ImportCoordinates $import_coordinates
		 * @param ImportLogger $logger
		 * @param Relations $relations
		 *
		 * @since TBD
		 */
		do_action( 'msls_content_import_after_import', $import_coordinates, $this->logger, $this->relations );

		/**
		 * Filters the data after the import ran.
		 *
		 * @param array $post_fields
		 * @param ImportCoordinates $import_coordinates
		 * @param ImportLogger $logger
		 * @param Relations $relations
		 */
		return apply_filters(
			'msls_content_import_data_after_import',
			$post_fields,
			$import_coordinates,
			$this->logger,
			$this->relations
		);
	}

	/**
	 * @param int                  $blog_id
	 * @param int                  $post_id
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	protected function update_inserted_blog_post_data( $blog_id, $post_id, array $data ) {
		$data['ID']          = $post_id;
		$data['post_status'] = empty( $data['post_status'] ) || 'auto-draft' === $data['post_status']
			? 'draft'
			: $data['post_status'];
		$this->insert_blog_post( $blog_id, $data );

		return $data;
	}

	/**
	 * @param int $dest_blog_id
	 * @param int $post_id
	 * @return void
	 */
	protected function redirect_to_blog_post( $dest_blog_id, $post_id ) {
		switch_to_blog( $dest_blog_id );
		$edit_post_link = html_entity_decode( get_edit_post_link( $post_id ) );
		wp_safe_redirect( $edit_post_link );
		die();
	}

	/**
	 * Filters whether the post should be considered empty or not.
	 *
	 * Empty posts would not be saved to database but it's fine if in
	 * the context of a content import as it will be populated.
	 *
	 * @param bool $is_empty
	 *
	 * @return bool
	 */
	public function filter_empty( $is_empty ) {
		if ( ! $this->main->verify_nonce() ) {
			return $is_empty;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['msls_import'] ) ) {
			return $is_empty;
		}

		return false;
	}
}
