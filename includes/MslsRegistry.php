<?php
/**
 * MslsRegistry
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Registry instead of singletons
 * @package Msls
 */
class MslsRegistry {

	/**
	 * Generic container
	 *
	 * @var string[]
	 */
	private static $arr = [];

	/**
	 * Instance
	 *
	 * @var MslsRegistry
	 */
	private static $instance;

	/**
	 * Get an object by key
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	private function get( $key ) {
		return self::$arr[ $key ] ?? null;
	}

	/**
	 * Set an object
	 *
	 * @param string $key
	 * @param mixed $instance
	 */
	private function set( string $key, $instance ): void {
		self::$arr[ $key ] = $instance;
	}

	/**
	 * Registry is a singleton
	 *
	 * @return MslsRegistry
	 */
	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Static get_object calls get
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public static function get_object( $key ) {
		return self::instance()->get( $key );
	}

	/**
	 * Static set_object calls set
	 *
	 * @param string $key
	 * @param mixed $instance
	 */
	public static function set_object( string $key, $instance ): void {
		self::instance()->set( $key, $instance );
	}

}
