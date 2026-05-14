<?php declare( strict_types=1 );

namespace lloc\Msls\Link;

use lloc\Msls\LinkInterface;

/**
 * Link type: Image only
 *
 * @package Msls
 */
class ImageOnly extends Link implements LinkInterface {

	/**
	 * Output format
	 *
	 * @var string
	 */
	protected $format_string = '<img src="{src}" alt="{alt}"/>';

	/**
	 * Get the description
	 *
	 * @return string
	 */
	public static function get_description(): string {
		return __( 'Flag only', 'multisite-language-switcher' );
	}
}
