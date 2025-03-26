<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * MslsOptionsTaxTerm
 *
 * @package Msls
 */
class MslsOptionsTaxTerm extends MslsOptionsTax implements OptionsTaxInterface {

	const BASE_OPTION = 'tag_base';

	const BASE_DEFINED = 'tag';

	/**
	 * Rewrite with front
	 *
	 * @var bool
	 */
	public ?bool $with_front = true;

	public function handle_rewrite(): OptionsTaxInterface {
		add_filter( 'msls_get_postlink', array( $this, 'check_base' ), 9, 2 );

		return $this;
	}

	/**
	 * Check and correct URL
	 *
	 * @param mixed              $url
	 * @param MslsOptionsTaxTerm $options
	 *
	 * @return string
	 */
	public function check_base( $url, $options ) {
		if ( ! is_string( $url ) || empty( $url ) ) {
			return $url;
		}

		$tax_query    = $options->get_tax_query();
		$base_defined = self::get_base_defined( $tax_query );
		$base_option  = self::get_base_option();

		if ( $base_defined != $base_option ) {
			$search  = '/' . $base_defined . '/';
			$replace = '/' . $base_option . '/';
			$url     = str_replace( $search, $replace, $url );
		}

		return $url;
	}

	protected static function get_base_defined( string $tax_query ): string {
		global $wp_rewrite;

		$permastruct = $wp_rewrite->get_extra_permastruct( $tax_query );
		if ( $permastruct ) {
			$permastruct = explode( '/', $permastruct );
			end( $permastruct );
			$permastruct = prev( $permastruct );
			if ( false !== $permastruct ) {
				return $permastruct;
			}
		}

		return static::BASE_DEFINED;
	}

	public static function get_base_option(): string {
		$base_option = get_option( static::BASE_OPTION, '' );

		return $base_option ?: static::BASE_DEFINED;
	}
}
