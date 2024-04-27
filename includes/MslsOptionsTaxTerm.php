<?php
/**
 * MslsOptionsTaxTerm
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

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
	 * Rewrite with front
	 * @var bool
	 */
	public $with_front = true;

	/**
	 * Check and correct URL
	 *
	 * @param string $url
	 * @param MslsOptions $options
	 *
	 * @return string
	 */
	public function check_base( $url, $options ) {
		if ( ! is_string( $url ) || empty( $url ) ) {
			return $url;
		}

		global $wp_rewrite;

		$base_defined = $options->base_defined;

		$permastruct = $wp_rewrite->get_extra_permastruct( $options->get_tax_query() );
		if ( $permastruct ) {
			$permastruct = explode( '/', $permastruct );
			end( $permastruct );
			$permastruct = prev( $permastruct );
			if ( false !== $permastruct ) {
				$base_defined = $permastruct;
			}
		}

		$base_option = get_option( $options->base_option );
		if ( empty( $base_option ) ) {
			$base_option = $options->base_defined;
		}

		if ( $base_defined != $base_option ) {
			$search  = '/' . $base_defined . '/';
			$replace = '/' . $base_option . '/';
			$count   = 1;
			$url     = str_replace( $search, $replace, $url, $count );
		}

		return $url;
	}

}
