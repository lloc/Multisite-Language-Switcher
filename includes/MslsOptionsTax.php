<?php
/**
 * MslsOptionsTax
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Taxonomy options
 * @package Msls
 */
class MslsOptionsTax extends MslsOptions {

	/**
	 * Separator
	 * @var string
	 */
	protected $sep = '_term_';

	/**
	 * Autoload
	 * @var string
	 */
	protected $autoload = 'no';

	/**
	 * Factory method
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $id
	 *
	 * @return MslsOptionsTax
	 */
	public static function create( $id = 0 ) {
		$id  = ! empty( $id ) ? (int) $id : get_queried_object_id();
		$req = '';

		if ( is_admin() ) {
			$req = MslsContentTypes::create()->acl_request();
		} elseif ( is_category() ) {
			$req = 'category';
		} elseif ( is_tag( $id ) ) {
			$req = 'post_tag';
		}

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

		if ( $req ) {
			add_filter( 'check_url', [ $options, 'check_base' ], 9, 2 );
		} else {
			global $wp_rewrite;

			$options->with_front = ! empty( $wp_rewrite->extra_permastructs[ $options->get_tax_query() ]['with_front'] );
		}

		return $options;
	}

	/**
	 * Get the queried taxonomy
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
		$url = '';

		if ( $this->has_value( $language ) ) {
			$url = $this->get_term_link( (int) $this->__get( $language ) );
		}

		return apply_filters( 'check_url', $url, $this );
	}

	/**
	 * Get current link
	 * @return string
	 */
	public function get_current_link() {
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

}
