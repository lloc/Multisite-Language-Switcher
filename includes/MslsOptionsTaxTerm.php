<?php

namespace lloc\Msls;

/**
 * Tag options
 *
 * @package Msls
 */
class MslsOptionsTaxTerm extends MslsOptionsTax {

	/**
	 * @var string
	 */
	protected $base_option = 'tag_base';

	/**
	 * @var string
	 */
	protected $base_defined = 'tag';

	/**
	 * @var bool
	 */
	public $with_front = true;

	/**
	 * Check and correct URL
	 *
	 * @param mixed $url
	 * @param MslsOptionsTaxTerm $options
	 *
	 * @return string
	 */
	public function check_base( $url, $options ) {
		if ( ! is_string( $url ) || empty( $url ) ) {
			return $url;
		}

		$search  = '/' . self::get_base_defined( $options->get_tax_query(), $options->base_defined ) . '/';
		$replace = '/' . self::get_base_option( $options->base_option, $options->base_defined ) . '/';

		return $search != $replace ? str_replace( $search, $replace, $url ): $url;
	}

	/**
	 * @param string $tax_query_name
	 * @param string $default
	 *
	 * @return string
	 */
	public static function get_base_defined( string $tax_query_name, string $default ): string {
		global $wp_rewrite;

		$permastruct = $wp_rewrite ? $wp_rewrite->get_extra_permastruct( $tax_query_name ) : false;
		if ( ! $permastruct ) {
			return $default;
		}

		$permastruct = explode( '/', $permastruct );
		end( $permastruct );
		$permastruct = prev( $permastruct );

		return ! $permastruct ? $default : $permastruct;
	}

	/**
	 * @param string $option_name
	 * @param string $default
	 *
	 * @return string
	 */
	public static function get_base_option( string $option_name, string $default ): string {
		$base_option = get_option( $option_name );

		return empty( $base_option ) ? $default : $base_option;
	}

}
