<?php declare( strict_types=1 );

namespace lloc\Msls\Registry;

/**
 * Base class for singleton instances backed by the Registry.
 *
 * @package lloc\Msls\Registry
 */
class Instance {

	/**
	 * Gets or creates an instance of the called class
	 *
	 * @return static
	 */
	public static function instance() {
		$class = get_called_class();
		$obj   = Registry::get_object( $class );

		if ( ! $obj ) {
			$obj = new $class();

			Registry::set_object( $class, $obj );
		}

		return $obj;
	}
}
