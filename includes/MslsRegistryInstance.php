<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * Class MslsRegistryInstance
 *
 * @package lloc\Msls
 */
class MslsRegistryInstance {

	/**
	 * Gets or creates an instance of the called class
	 *
	 * @return static
	 */
	public static function instance() {
		$class = get_called_class();

		if ( ! ( $obj = MslsRegistry::get_object( $class ) ) ) {
			$obj = new $class();

			MslsRegistry::set_object( $class, $obj );
		}

		return $obj;
	}
}
