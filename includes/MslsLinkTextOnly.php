<?php

namespace lloc\Msls;

/**
 * Link type: Text only
 *
 * @package Msls
 */
class MslsLinkTextOnly extends MslsLink {

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
