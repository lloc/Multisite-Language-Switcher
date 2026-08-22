<?php declare( strict_types=1 );

namespace lloc\Msls;

use DI\Container as DiContainer;
use DI\ContainerBuilder;

/**
 * Accessor for the PHP-DI container.
 *
 * The container is built on first use and kept for the rest of the request; requests that
 * never ask for it never pay for it.
 *
 * @package Msls
 */
final class Container {

	/**
	 * The container built by self::get(), kept for the rest of the request.
	 *
	 * @var ?DiContainer
	 */
	private static ?DiContainer $container = null;

	/**
	 * @throws \Exception If the container cannot be built.
	 */
	public static function get(): DiContainer {
		if ( null === self::$container ) {
			$builder = new ContainerBuilder();
			$builder->addDefinitions( Plugin::plugin_dir_path( 'config.php' ) );

			self::$container = $builder->build();
		}

		return self::$container;
	}

	/**
	 * Drops the built container, so the next call to self::get() builds a new one.
	 */
	public static function reset(): void {
		self::$container = null;
	}
}
