<?php

namespace lloc\Msls\ContentImport\Importers\Terms;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\BaseImporter;
use lloc\Msls\MslsOptionsTax;
use lloc\Msls\MslsOptionsTaxTerm;

/**
 * Class ShallowDuplicating
 *
 * Duplicates, if needed, the terms assigned to the post without recursion for hierarchical taxonomies.
 *
 * @package lloc\Msls\ContentImport\Importers\Terms
 */
class ShallowDuplicating extends BaseImporter {

	const TYPE = 'shallow-duplicating';

	/**
	 * @var array
	 */
	protected $reset_taxonomies = array();

	/**
	 * Returns an array of information about the importer.
	 *
	 * @return \stdClass
	 */
	public static function info() {
		return (object) array(
			'slug'        => static::TYPE,
			'name'        => __( 'Shallow Duplicating', 'multisite-language-switcher' ),
			'description' => __(
				'Shallow (one level deep) duplication or assignment of the source post taxonomy terms to the destination post.',
				'multisite-language-switcher'
			),
		);
	}

	public function import( array $data ) {
		$source_blog_id = $this->import_coordinates->source_blog_id;
		$source_post_id = $this->import_coordinates->source_post_id;
		$dest_post_id   = $this->import_coordinates->dest_post_id;
		$dest_lang      = $this->import_coordinates->dest_lang;

		switch_to_blog( $source_blog_id );

		$source_terms     = wp_get_post_terms( $source_post_id, get_taxonomies() );
		$source_terms_ids = wp_list_pluck( $source_terms, 'term_id' );
		$msls_terms       = array_combine(
			$source_terms_ids,
			array_map( array( MslsOptionsTaxTerm::class, 'create' ), $source_terms_ids )
		);

		switch_to_blog( $this->import_coordinates->dest_blog_id );

		/** @var \WP_Term $term */
		foreach ( $source_terms as $term ) {
			// is there a translation for the term in this blog?
			$msls_term    = $msls_terms[ $term->term_id ];
			$dest_term_id = $msls_term->{$dest_lang};

			if ( null === $dest_term_id ) {
				$dest_term_id = $this->create_local_term( $term, $msls_term, $dest_lang );
			}

			if ( false === $dest_term_id ) {
				continue;
			}

			$added = $this->update_object_terms( $dest_post_id, $dest_term_id, $term->taxonomy );

			if ( is_array( $added ) && ! count( array_filter( $added ) ) ) {
				// while we think the term translation exists it might not, let's create it
				$dest_term_id = $this->create_local_term( $term, $msls_term, $dest_lang );

				if ( false === $dest_term_id ) {
					continue;
				}

				// and try again
				$added = $this->update_object_terms( $dest_post_id, $dest_term_id, $term->taxonomy );
			}

			if ( $added instanceof \WP_Error ) {
				$this->logger->log_error( "term/added/{$term->taxonomy}", array( $term->name => $term->term_id ) );
			} else {
				$this->logger->log_success( "term/added/{$term->taxonomy}", array( $term->name => $term->term_id ) );
			}
		}

		restore_current_blog();

		return $data;
	}

	/**
	 * @param \WP_Term           $term
	 * @param MslsOptionsTaxTerm $msls_term
	 * @param string             $dest_lang
	 *
	 * @return bool|int
	 */
	protected function create_local_term( \WP_Term $term, MslsOptionsTax $msls_term, $dest_lang ) {
		$meta         = get_term_meta( $term->term_id );
		$dest_term_id = wp_create_term( $term->name, $term->taxonomy );

		if ( $dest_term_id instanceof \WP_Error ) {
			$this->logger->log_error( "term/created/{$term->taxonomy}", array( $term->name ) );

			return false;
		}

		$dest_term_id = (int) reset( $dest_term_id );
		$this->relations->should_create( $msls_term, $dest_lang, $dest_term_id );
		$this->logger->log_success( "term/created/{$term->taxonomy}", array( $term->name => $term->term_id ) );
		$meta = $this->filter_term_meta( $meta, $term );
		if ( ! empty( $meta ) ) {
			foreach ( $meta as $key => $value ) {
				add_term_meta( $dest_term_id, $key, $value );
			}
		}

		return $dest_term_id;
	}

	/**
	 * @param array    $meta
	 * @param \WP_Term $term
	 *
	 * @return array
	 */
	protected function filter_term_meta( array $meta, \WP_Term $term ) {
		/**
		 * Filters the list of term meta that should not be imported for a term.
		 *
		 * @param array $blacklist
		 * @param \WP_Term $term
		 * @param array $meta
		 * @param ImportCoordinates $import_coordinates
		 */
		$blacklist = apply_filters(
			'msls_content_import_term_meta_blacklist',
			array(),
			$term,
			$meta,
			$this->import_coordinates
		);

		return array_diff_key( $meta, array_combine( $blacklist, $blacklist ) );
	}

	/**
	 * @param int    $object_id
	 * @param int    $dest_term_id
	 * @param string $taxonomy
	 *
	 * @return array|\WP_Error
	 */
	protected function update_object_terms( $object_id, $dest_term_id, $taxonomy ) {
		if ( ! in_array( $taxonomy, $this->reset_taxonomies, true ) ) {
			wp_set_object_terms( $object_id, array(), $taxonomy );
			$this->reset_taxonomies[] = $taxonomy;
		}

		return wp_add_object_terms( $object_id, $dest_term_id, $taxonomy );
	}
}
