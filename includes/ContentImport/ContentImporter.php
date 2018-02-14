<?php

namespace lloc\Msls\ContentImport;


use lloc\Msls\ContentImport\Importers\AttachmentsImporters;
use lloc\Msls\ContentImport\Importers\Importer;
use lloc\Msls\ContentImport\Importers\PostFieldsImporters;
use lloc\Msls\ContentImport\Importers\PostMetaImporters;
use lloc\Msls\ContentImport\Importers\PostThumbnailImporters;
use lloc\Msls\ContentImport\Importers\TermsImporters;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsRegistryInstance;

class ContentImporter extends MslsRegistryInstance {

	/**
	 * @var \MslsMain
	 */
	protected $main;

	public function __construct( MslsMain $main = null ) {
		$this->main = null !== $main ?: MslsMain::init();
	}

	public function on_wp_insert_post( array $data ) {
		if ( ! $this->pre_flight_check() || false === $sources = $this->parse_sources() ) {
			return $data;
		}

		list( $source_blog_id, $source_post_id ) = $sources;

		if ( $source_blog_id === get_current_blog_id() ) {
			return $data;
		}

		$dest_post_id = get_the_ID();
		$dest_blog_id = get_current_blog_id();

		if ( empty( $dest_post_id ) ) {
			return $data;
		}

		// @todo action before import
		// @todo filter data before import

		switch_to_blog( $source_blog_id );
		$source_post = get_post( $source_post_id );
		restore_current_blog();

		if ( ! $source_post instanceof \WP_Post ) {
			return $data;
		}

		$import_coordinates              = new ImportCoordinates( $source_blog_id, $source_post_id, $dest_blog_id, $dest_post_id );
		$import_coordinates->source_post = $source_post;

		$importers = [
			'post-fields'    => PostFieldsImporters::make(),
			'post-meta'      => PostMetaImporters::make(),
			'terms'          => TermsImporters::make(),
			'post-thumbnail' => PostThumbnailImporters::make(),
			'attachments'    => AttachmentsImporters::make(),
		];

		// @todo filter the map here

		$log       = new ImportLog();
		$relations = new Relations();

		foreach ( $importers as $key => $importer ) {
			/** @var Importer $importer */
			$importer->set_import_coordinates( $import_coordinates );
			$data = $importer->import( $data );
			$log->merge( $importer->get_log() );
			$relations->merge( $importer->get_relations() );
		}

		$relations->relate();
		$log->log();

		// @todo action after import
		// @todo filter data after import

		return $data;

		//		$sourceTerms                   = wp_get_post_terms( $source_post_id, get_taxonomies() );
		//		$sourceTermIds                 = wp_list_pluck( $sourceTerms, 'term_id' );
		//		$mslsTerms                     = array_combine(
		//			$sourceTermIds,
		//			array_map( array( 'MslsOptionsTaxTerm', 'create' ), $sourceTermIds )
		//		);
		//		$sourceCustom                  = get_post_custom( $source_post_id );
		//		$sourcePostThumbnailId         = (int) get_post_thumbnail_id( $source_post_id );
		//		$sourcePostThumbnailAttachment = get_post( $sourcePostThumbnailId );
		//		$sourcePostThumbnailMeta       = $sourcePostThumbnailAttachment instanceof \WP_Post ?
		//			wp_get_attachment_metadata( $sourcePostThumbnailId )
		//			: false;
		//		$sourceUploadDir               = wp_upload_dir();
		//
		//		$relate = array(
		//			array( \MslsOptionsPost::create( $source_post_id ), $dest_lang, $dest_post_id ),
		//		);
		//
		//		restore_current_blog();
		//
		//		$fields = array(
		//			'post_content',
		//			'post_content_filtered',
		//			'post_title',
		//			'post_excerpt',
		//		);
		//
		//		foreach ( $fields as $field ) {
		//			$data[ $field ] = $sourcePost->{$field};
		//		}
		//
		//		$log = array(
		//			'success' => array(
		//				'term' => array(
		//					'create' => array(),
		//				),
		//			),
		//			'error'   => array(
		//				'term' => array(
		//					'create' => array(),
		//				),
		//			),
		//		);
		//
		//		/** @var \WP_Term $term */
		//		foreach ( $sourceTerms as $term ) {
		//			// is there a translation for the term in this blog?
		//			$mslsTerm   = $mslsTerms[ $term->term_id ];
		//			$destTermId = $mslsTerm->{$dest_lang};
		//			if ( null === $destTermId ) {
		//				$meta = get_term_meta( $term->term_id );
		//				// @todo note here that slug, parent and the like are not set; by design to avoid recursive population
		//				$destTermId = wp_create_term( $term->name, $term->taxonomy );
		//				if ( $destTermId instanceof \WP_Error ) {
		//					$log['error']['term']['create'][] = array( $term->name, $term->taxonomy );
		//					continue;
		//				}
		//				$destTermId                         = (int) reset( $destTermId );
		//				$relate[]                           = array( $mslsTerm, $dest_lang, $destTermId );
		//				$log['success']['term']['create'][] = array( $term->name, $term->taxonomy );
		//				$meta                               = $this->filterTermMeta( $meta );
		//				if ( ! empty( $meta ) ) {
		//					foreach ( $meta as $key => $value ) {
		//						add_term_meta( $destTermId, $key, $value );
		//					}
		//				}
		//			}
		//			$added = wp_add_object_terms( $dest_post_id, $destTermId, $term->taxonomy );
		//			if ( $added instanceof \WP_Error ) {
		//				$log['error']['term']['added'] = array( $term->name, $term->id );
		//			} else {
		//				$log['success']['term']['added'] = array( $term->name, $term->id );
		//			}
		//		}
		//
		//		$sourceCustom = $this->filterPostMeta( $sourceCustom );
		//		foreach ( $sourceCustom as $key => $customEntries ) {
		//			if ( '' !== get_post_meta( $dest_post_id, $key ) ) {
		//				foreach ( $customEntries as $entry ) {
		//					add_post_meta( $dest_post_id, $key, $entry );
		//					$log['success']['custom']['added'] = array( $key, $entry );
		//				}
		//			}
		//		}
		//
		//		if ( empty( get_post_thumbnail_id( $dest_post_id ) ) && $sourcePostThumbnailAttachment instanceof \WP_Post ) {
		//			$sourcePostThumbnailFile = $sourceUploadDir['basedir'] . '/' . $sourcePostThumbnailMeta['file'];
		//
		//			// Check the type of file. We'll use this as the 'post_mime_type'.
		//			$filetype = wp_check_filetype( basename( $sourcePostThumbnailFile ), null );
		//
		//			// Prepare an array of post data for the attachment.
		//			$attachment = array(
		//				'guid'           => $sourcePostThumbnailAttachment->guid,
		//				'post_mime_type' => $filetype['type'],
		//				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $sourcePostThumbnailFile ) ),
		//				'post_content'   => '',
		//				'post_status'    => 'inherit',
		//			);
		//
		//			// Insert the attachment.
		//			$destPostThumbnailId = wp_insert_attachment( $attachment, $sourcePostThumbnailFile, $dest_post_id );
		//
		//			if ( empty( $destPostThumbnailId ) ) {
		//				$log['error']['postThumbnail']['created'] = array( __( 'no', 'alicetour' ) );
		//			} else {
		//				$log['success']['postThumbnail']['created'] = array( $destPostThumbnailId );
		//			}
		//
		//			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		//			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		//
		//			// Generate the metadata for the attachment, and update the database record.
		//			$destPostThumbnailMeta = wp_generate_attachment_metadata( $destPostThumbnailId, $sourcePostThumbnailFile );
		//			wp_update_attachment_metadata( $destPostThumbnailId, $destPostThumbnailMeta );
		//
		//			$destPostThumbnailSet = set_post_thumbnail( $dest_post_id, $destPostThumbnailId );
		//
		//			update_post_meta( $destPostThumbnailId, '_msls_imported', array( 'blog' => $source_blog_id, 'post' => $sourcePostThumbnailId ) );
		//
		//			if ( $destPostThumbnailSet ) {
		//				$log['success']['postThumbnail']['set'] = array( $destPostThumbnailId );
		//			} else {
		//				$log['error']['postThumbnail']['set'] = array( $destPostThumbnailId );
		//			}
		//
		//			// @todo should dest post thumbnail be related with the source one?
		//		}
		//
		//		switch_to_blog( $source_blog_id );
		//		foreach ( $relate as $r ) {
		//			/** @var \MslsOptions $option */
		//			list( $option, $lang, $id ) = $r;
		//			$option->save( array( $lang => $id ) );
		//		}
		//		restore_current_blog();
		//
		//		return $data;
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

	public function filter_empty( $empty ) {

	}
}