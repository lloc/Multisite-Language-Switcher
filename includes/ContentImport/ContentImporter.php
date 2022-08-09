<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\Importers\Importer;
use lloc\Msls\ContentImport\Importers\Map;
use lloc\Msls\ContentImport\Importers\WithRequestPostAttributes;
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
	use WithRequestPostAttributes;

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
	 * @var bool Whether the class should handle requests or not.
	 */
	protected $handle = true;

	/**
	 * @var int The ID of the post the class created while handling the request, if any.
	 */
	protected $has_created_post = 0;

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
	 * Handles an import request happening during a post save or a template redirect.
	 *
	 * @param array|null $data
	 *
	 * @return array The updated, if needed, data array.
	 */
	public function handle_import( array $data = [] ) {
		if ( ! $this->pre_flight_check() || false === $sources = $this->parse_sources() ) {
			return $data;
		}
		

		list( $source_blog_id, $source_post_id ) = $sources;
		
		
		
		// Handle relations between posts 
		$relations = [];
		$relations = $this->parse_relations();
		
		
		if ( $source_blog_id === get_current_blog_id() ) {
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

		$data = $this->import_content( $import_coordinates, $data, $relations );

		if ( $this->has_created_post ) {
			$this->update_inserted_blog_post_data($dest_blog_id, $dest_post_id, $data );
			
		}

		$this->redirect_to_blog_post( $dest_blog_id, $dest_post_id, $relations );
		
		return $data;
	}

	/**
	 * Whether the importer should run or not.
	 *
	 * @return bool
	 */
	protected function pre_flight_check( array $data = [] ) {
		if ( ! $this->handle ) {
			return false;
		}

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
	 * Parses the source blog and post IDs from the $_POST array validating them.
	 *
	 * @return array|bool
	 */
	public function parse_relations() {
		if ( ! isset( $_POST['msls_relations'] ) ) {
			return false;
		}

		$import_rels = array_filter( explode( ',', trim(  $_POST['msls_relations'] )) );
	
		if ( count( $import_rels ) < 1 ) {
			return false;
		}

		return $import_rels;
	}

	protected function get_the_blog_post_ID( $blog_id ) {
		switch_to_blog( $blog_id );

		$id = get_the_ID();

		if ( ! empty( $id ) ) {
			restore_current_blog();

			return $id;
		}

		if ( isset( $_REQUEST['post'] ) && filter_var( $_REQUEST['post'], FILTER_VALIDATE_INT ) ) {
			return (int) $_REQUEST['post'];
		}

		$data = [
			'post_type'  => $this->read_post_type_from_request( 'post' ),
			'post_title' => 'MSLS Content Import Draft - ' . date( 'Y-m-d H:i:s' )
		];

		return $this->insert_blog_post( $blog_id, $data );
	}

	protected function insert_blog_post( $blog_id, array $data = [] ) {
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

		$this->has_created_post = $post_id ?: false;

		restore_current_blog();

		return $this->has_created_post;
	}

	public function handle( $handle ) {
		$this->handle = $handle;

		// also prevent MSLS from saving
		if ( false === $handle ) {
			add_action( 'msls_main_save', '__return_false' );
		} else {
			remove_action( 'msls_main_save', '__return_false' );
		}
	}

	/**
	 * Imports content according to the provided coordinates.
	 *
	 * @param       ImportCoordinates $import_coordinates
	 * @param array $post_fields An optional array of post fields; this can be
	 *                                             left empty if the method is not called as a consequence
	 *                                             of filtering the `wp_insert_post_data` filter.
	 *
	 * @return array An array of modified post fields.
	 */
	public function import_content( ImportCoordinates $import_coordinates, array $post_fields = []) {
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
		 * @since TBD
		 *
		 * @param array $post_fields
		 * @param ImportCoordinates $import_coordinates
		 */
		$post_fields = apply_filters( 'msls_content_import_data_before_import', $post_fields, $import_coordinates );

		/**
		 * Filters the importers map before it's populated.
		 *
		 * Returning a non `null` value here will override the creation of the importers map completely
		 * and use the one returned in the filter.
		 *
		 * @param null $importers
		 * @param ImportCoordinates $import_coordinates
		 */
		$importers = apply_filters( 'msls_content_import_importers', null, $import_coordinates );

		if ( null === $importers ) {
			$importers = Map::instance()->make( $import_coordinates );
		}

		$this->logger    = $this->logger ?: new ImportLogger( $import_coordinates );
		$this->relations = $this->relations ?: new Relations( $import_coordinates );

		
		if ( ! empty( $importers ) && is_array( $importers ) ) {
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
		 * @since TBD
		 *
		 * @param ImportCoordinates $import_coordinates
		 * @param ImportLogger $logger
		 * @param Relations $relations
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
		return apply_filters( 'msls_content_import_data_after_import', $post_fields, $import_coordinates, $this->logger, $this->relations );
	}

	/**
	 * @param array $data
	 * @param int $post_id
	 *
	 * @return array
	 */
	protected function update_inserted_blog_post_data($blog_id, $post_id, array $data ) {
		$data['ID']          = $post_id;
		$data['post_status'] = empty( $data['post_status'] ) || $data['post_status'] === 'auto-draft'
			? 'draft'
			: $data['post_status'];
		$this->insert_blog_post( $blog_id, $data );

		return $data;
	}

	protected function redirect_to_blog_post( $dest_blog_id, $post_id, array $relations = [] ) {
		
		
		switch_to_blog( $dest_blog_id );
		
		$path = get_edit_post_link( $post_id );
		
		// add relations to other languages
		if (count($relations) > 0) {
					
			// Add relations to other langs
			foreach ($relations as $ext_rel) {
				$ext_rel = explode('|', $ext_rel);
				list( $relation_blog_id, $relation_post_id ) = $ext_rel;
				
				// Check if relations are valid WP posts 
				switch_to_blog( $relation_blog_id );
				$relation_post = get_post( $relation_post_id );
				$relation_languages[] =  MslsBlogCollection::get_blog_language( $relation_blog_id );
				restore_current_blog();

				if ( ! $relation_post instanceof \WP_Post ) {
					continue;
				}
				
				// Add verified ids 
				$relation_ids[] = $relation_post_id;
			}
			
			if ( null !== $relation_ids && null !== $relation_languages ) {
				$path = add_query_arg( [ 'msls_id' => implode(',', $relation_ids), 'msls_lang' => implode(',', $relation_languages) ], $path );
			}	
			
		
		}
		
		$edit_post_link = html_entity_decode( $path );
		wp_redirect( $edit_post_link );
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
