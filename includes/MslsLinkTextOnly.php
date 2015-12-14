<?php
/**
 * MslsLinkTextOnly
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

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
	 * Get the description
	 * @return string
	 */
	public static function get_description() {
		return __( 'Description only', 'multisite-language-switcher' );
	}

}
