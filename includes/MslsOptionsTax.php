<?php
/**
 * MslsOptionsTax
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

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
	 * @param int $id
	 *
	 * @return MslsOptionsTax
	 */
	public static function create( $id = 0 ) {
		if ( is_admin() ) {
			$obj = MslsContentTypes::create();

			$id  = (int) $id;
			$req = $obj->acl_request();
		} else {
			$id  = get_queried_object_id();
			$req = ( is_category() ? 'category' : ( is_tag() ? 'post_tag' : '' ) );
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
			add_filter( 'check_url', array( $options, 'check_base' ), 9, 2 );
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

		return (
		isset( $wp_query->tax_query->queries[0]['taxonomy'] ) ?
			$wp_query->tax_query->queries[0]['taxonomy'] :
			''
		);
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
