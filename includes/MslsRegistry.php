<?php
/**
 * MslsRegistry
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Registry instead of singletons
 * @package Msls
 */
class MslsRegistry {

	/**
	 * Generic container
	 * @var array
	 */
	private static $arr = array();

	/**
	 * Instance
	 * @var MslsRegistry
	 */
	private static $instance;

	/**
	 * Constructor
	 *
	 * Don't call me directly!
	 * @codeCoverageIgnore
	 */
	final private function __construct() { }

	/**
	 * Clone
	 *
	 * Don't call me directly!
	 * @codeCoverageIgnore
	 */
	final private function __clone() { }

	/**
	 * Get an object by key
	 * @param string $key
	 * @return mixed
	 */
	private function get( $key ) {
		return( isset( self::$arr[ $key ] ) ? self::$arr[ $key ] : null );
	}

	/**
	 * Set an object
	 * @param string $key
	 * @param mixed $instance
	 */
	private function set( $key, $instance ) {
		self::$arr[ $key ] = $instance;
	}

	/**
	 * Registry is a singleton
	 * @return MslsRegistry
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Static get_object calls get
	 * @param string $key
	 * @return mixed
	 */
	public static function get_object( $key ) {
		return self::instance()->get( $key );
	}

	/**
	 * Static set_object calls set
	 * @param string $key
	 * @param mixed $instance
	 */
	public static function set_object( $key, $instance ) {
		self::instance()->set( $key, $instance );
	}

}
