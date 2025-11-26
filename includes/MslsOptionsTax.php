<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * MslsOptionsTax
 *
 * @package Msls
 */
class MslsOptionsTax extends MslsOptions implements OptionsTaxInterface {

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
				$options = new MslsOptionsTaxTermCategory( $id );
				break;
			case 'post_tag':
				$options = new MslsOptionsTaxTerm( $id );
				break;
			default:
				$options = new MslsOptionsTax( $id );
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

		$post_link = apply_filters_deprecated( 'check_url', array( $post_link, $this ), '2.7.1', MslsOptions::MSLS_GET_POSTLINK_HOOK );

		return apply_filters( MslsOptions::MSLS_GET_POSTLINK_HOOK, $post_link, $this );
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
