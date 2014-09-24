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
			$base = get_option( $this->base_option );

			if ( !empty( $base ) && $this->base_defined != $base ) {
				$search  = '/' . $this->base_defined . '/';
				$replace = '/' . $base . '/';
				$count   = 1;
				$url     = str_replace( $search, $replace, $url, $count );
			}
		}

		return $url;
	}

}
