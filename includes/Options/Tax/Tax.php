<?php declare( strict_types=1 );

namespace lloc\Msls\Options\Tax;

use lloc\Msls\Options\Options;

/**
 * OptionsTax
 *
 * @package Msls
 */
class Tax extends Options implements OptionsTaxInterface {

	public const SEPARATOR = '_term_';

	/**
	 * @var bool
	 */
	protected bool $autoload = false;

	/**
	 * @param int $id
	 *
	 * @return OptionsTaxInterface
	 */
	public static function create( $id = 0 ): OptionsTaxInterface {
		$id  = ! empty( $id ) ? (int) $id : get_queried_object_id();
		$req = self::get_content_type( $id );

		switch ( $req ) {
			case 'category':
				$options = new Category( $id );
				break;
			case 'post_tag':
				$options = new Term( $id );
				break;
			default:
				$options = new Tax( $id );
		}

		return $options->handle_rewrite();
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public static function get_content_type( int $id ): string {
		if ( is_admin() ) {
			return msls_content_types()->acl_request();
		}

		return ( is_category( $id ) ? 'category' : ( is_tag( $id ) ? 'post_tag' : '' ) );
	}

	public function handle_rewrite(): OptionsTaxInterface {
		global $wp_rewrite;

		$this->with_front = ! empty( $wp_rewrite->extra_permastructs[ $this->get_tax_query() ]['with_front'] );

		return $this;
	}

	/**
	 * Get the queried taxonomy
	 *
	 * @return string
	 */
	public function get_tax_query() {
		global $wp_query;

		if ( class_exists( 'WooCommerce' ) && is_woocommerce() && isset( $wp_query->tax_query->queries[1]['taxonomy'] ) ) {
			return $wp_query->tax_query->queries[1]['taxonomy'];
		} elseif ( isset( $wp_query->tax_query->queries[0]['taxonomy'] ) ) {
			return $wp_query->tax_query->queries[0]['taxonomy'];
		}

		return parent::get_tax_query();
	}

	/**
	 * Get postlink
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_postlink( $language ) {
		$post_link = '';

		if ( $this->has_value( $language ) ) {
			$post_link = $this->get_term_link( (int) $this->__get( $language ) );
		}

		$post_link = apply_filters_deprecated( 'check_url', array( $post_link, $this ), '2.7.1', Options::MSLS_GET_POSTLINK_HOOK );

		return apply_filters( Options::MSLS_GET_POSTLINK_HOOK, $post_link, $this ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- constant value is already prefixed with "msls_".
	}

	public function get_permalink( string $language ): string {
		return (string) apply_filters(
			'msls_options_get_permalink',
			$this->get_postlink( $language ),
			$language
		);
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return $this->get_term_link( $this->get_arg( 0, 0 ) );
	}

	/**
	 * Gets the number of pages this blog has for the current request
	 *
	 * @param string $language
	 * @param string $context
	 *
	 * @return int
	 */
	public function get_max_pages( string $language, string $context = self::PAGINATION_ARCHIVE ): int {
		if ( self::PAGINATION_ARCHIVE !== $context ) {
			return 0;
		}

		$taxonomy = $this->get_tax_query();
		$term_id  = $this->get_term_id( $language );

		if ( empty( $taxonomy ) || empty( $term_id ) ) {
			return 0;
		}

		$term = get_term( $term_id, $taxonomy );

		return $term instanceof \WP_Term ? self::posts_to_pages( self::count_term_posts( $term ) ) : 0;
	}

	/**
	 * Gets the term this options object builds a link for in the current blog
	 *
	 * @param string $language
	 *
	 * @return int
	 */
	protected function get_term_id( string $language ): int {
		if ( $this->has_value( $language ) ) {
			return (int) $this->__get( $language );
		}

		return ms_is_switched() ? 0 : $this->get_arg( 0, 0 );
	}

	/**
	 * Gets the number of published posts an archive of the term holds in the current blog
	 *
	 * @param \WP_Term $term
	 *
	 * @return int
	 */
	protected static function count_term_posts( \WP_Term $term ): int {
		$children = get_term_children( $term->term_id, $term->taxonomy );

		if ( is_wp_error( $children ) || empty( $children ) ) {
			return (int) $term->count;
		}

		$taxonomy = get_taxonomy( $term->taxonomy );

		return self::count_query_posts(
			array(
				'post_type' => $taxonomy instanceof \WP_Taxonomy ? $taxonomy->object_type : 'post',
				'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- the count of a hierarchical term is not available anywhere else.
					array(
						'taxonomy'         => $term->taxonomy,
						'field'            => 'term_id',
						'terms'            => $term->term_id,
						'include_children' => true,
					),
				),
			)
		);
	}

	/**
	 * Wraps the call to get_term_link
	 *
	 * @param int $term_id
	 *
	 * @return string
	 */
	public function get_term_link( $term_id ) {
		if ( ! empty( $term_id ) ) {
			$taxonomy = $this->get_tax_query();
			if ( ! empty( $taxonomy ) ) {
				$link = get_term_link( $term_id, $taxonomy );
				if ( ! is_wp_error( $link ) ) {
					return $link;
				}
			}
		}

		return '';
	}

	public static function get_base_option(): string {
		return '';
	}
}
