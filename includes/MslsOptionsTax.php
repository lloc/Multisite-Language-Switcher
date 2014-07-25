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
	 * @param int $id
	 * @return MslsOptionsTax
	 */
	public static function create( $id = 0 ) {
		if ( is_admin() ) {
			$obj = MslsContentTypes::create();

			$id  = (int) $id;
			$req = $obj->acl_request();
		}
		else {
			global $wp_query;

			$id  = $wp_query->get_queried_object_id();
			$req = ( is_category() ? 'category' : ( is_tag() ? 'post_tag' : '' ) );
		}

		if ( 'category' == $req ) {
			return new MslsOptionsTaxTermCategory( $id );
		}
		elseif ( 'post_tag' == $req ) {
			return new MslsOptionsTaxTerm( $id );
		}
		return new MslsOptionsTax( $id );
	}

	/**
	 * Get the queried taxonomy
	 * @return string
	 */
	public function get_tax_query() {
		global $wp_query;
		return(
			isset( $wp_query->tax_query->queries[0]['taxonomy'] ) ?
			$wp_query->tax_query->queries[0]['taxonomy'] :
			''
		);
	}

	/**
	 * Check and correct URL
	 * @param string $url
	 * @return string
	 */
	public function check_url( $url ) {
		return( empty( $url ) || ! is_string( $url ) ? '' : $url );
	}

	/**
	 * Get postlink
	 * @param string $language
	 * @return string
	 */
	public function get_postlink( $language ) {
		$url = '';
		if ( $this->has_value( $language ) ) {
			$taxonomy = $this->get_tax_query();
			$url      = $this->check_url(
				get_term_link(
					(int) $this->__get( $language ),
					$taxonomy
				)
			);
		}
		return $url;
	}

	/**
	 * Get current link
	 * @return string
	 */
	public function get_current_link() {
		$taxonomy = $this->get_tax_query();
		return(
			empty( $taxonomy ) ?
			'' :
			get_term_link( $this->get_arg( 0, '' ), $taxonomy )
		);
	}

}
