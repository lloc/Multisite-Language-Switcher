<?php

namespace lloc\Msls;

/**
 * Link type: Image only
 *
 * @package Msls
 */
class MslsLinkImageOnly extends MslsLink {

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
	public static function get_description() {
		return __( 'Flag only', 'multisite-language-switcher' );
	}
}
