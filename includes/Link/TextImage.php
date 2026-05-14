<?php declare( strict_types=1 );

namespace lloc\Msls\Link;

use lloc\Msls\LinkInterface;

/**
 * Link type: Text and image
 *
 * @package Msls
 */
class TextImage extends Link implements LinkInterface {

	/**
	 * Output format
	 *
	 * @var string
	 */
	protected $format_string = '{txt} <img src="{src}" alt="{alt}"/>';

	/**
	 * Get the description
	 *
	 * @return string
	 */
	public static function get_description(): string {
		return __( 'Description and flag', 'multisite-language-switcher' );
	}
}
