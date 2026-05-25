<?php declare( strict_types=1 );

namespace lloc\Msls\Link;

/**
 * Link type: Text only
 *
 * @package Msls
 */
class TextOnly extends Link implements LinkInterface {

	/**
	 * Output format
	 *
	 * @var string
	 */
	protected $format_string = '{txt}';

	/**
	 * Get the description
	 *
	 * @return string
	 */
	public static function get_description(): string {
		return __( 'Description only', 'multisite-language-switcher' );
	}
}
