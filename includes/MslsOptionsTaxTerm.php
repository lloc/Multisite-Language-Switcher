<?php
/**
 * MslsOptionsTaxTerm
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Tag options
 * @package Msls
 */
class MslsOptionsTaxTerm extends MslsOptionsTax {

	/**
	 * Base option
	 * @var string
	 */
	protected $base_option = 'tag_base';

	/**
	 * Base definition
	 * @var string
	 */
	protected $base_defined = 'tag';

	/**
	 * Check and correct URL
	 * @param string $url
	 * @return string
	 */
	public function check_url( $url ) {
		$url = parent::check_url( $url );

		if ( '' != $url ) {
			/* Custom structure for categories or tags */
			global $wp_rewrite;

			$bdefined = $this->base_defined;

			$struct = $wp_rewrite->get_extra_permastruct( $this->get_tax_query() );
			if ( $struct ) {
				$struct = explode( '/', $struct );
				end( $struct );
				$struct = prev( $struct );
				if ( false !== $struct ) {
					$bdefined = $struct;
				}
			}

			$boption = get_option( $this->base_option );
			if ( empty( $boption ) ) {
				$boption = $this->base_defined;
			}

			if ( $bdefined != $boption ) {
				$search  = '/' . $bdefined . '/';
				$replace = '/' . $boption . '/';
				$count   = 1;
				$url     = str_replace( $search, $replace, $url, $count );
			}
		}

		return $url;
	}

}
