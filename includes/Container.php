<?php declare( strict_types=1 );

namespace lloc\Msls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Accessor for the service container, built on first use
 *
 * @package Msls
 */
final class Container {

	/**
	 * The container built by self::get(), kept for the rest of the request
	 *
	 * @var ?ServiceContainer
	 */
	private static ?ServiceContainer $container = null;

	/**
	 * Gets the container of the current request
	 *
	 * @return ServiceContainer
	 */
	public static function get(): ServiceContainer {
		if ( null === self::$container ) {
			$definitions = require Plugin::plugin_dir_path( 'config.php' );

			self::$container = new ServiceContainer( is_array( $definitions ) ? $definitions : array() );
		}

		return self::$container;
	}

	/**
	 * Drops the built container, so the next call to self::get() builds a new one
	 */
	public static function reset(): void {
		self::$container = null;
	}
}
