<?php
/**
 * MslsLinkTextOnly
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Link type: Text only
 * @package Msls
 */
class MslsLinkTextOnly extends MslsLink {

	/**
	 * Output format
	 * @var string
	 */
	protected $format_string = '{txt}';

	/**
	 * @return string
	 */
	public static function get_description(): string {
		return __( 'Description only', 'multisite-language-switcher' );
	}

}
