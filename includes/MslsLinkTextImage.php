<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * Link type: Text and image
 *
 * @package Msls
 */
class MslsLinkTextImage extends MslsLink implements LinkInterface {

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
