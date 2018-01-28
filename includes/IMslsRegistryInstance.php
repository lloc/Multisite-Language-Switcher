<?php
/**
 * IMslsRegistryInstance
 * @author Dennis Ploetner <re@lloc.de>
 * @since 1.0.6
 */

namespace lloc\Msls;

/**
 * Interface IMslsRegistryInstance
 * @package lloc\Msls
 */
interface IMslsRegistryInstance {

	/**
	 * Returns an instance
	 *
	 * @return object
	 */
	public static function instance();

}