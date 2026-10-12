<?php declare( strict_types=1 );

namespace lloc\Msls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Container for the service definitions of config.php
 *
 * @package Msls
 */
class ServiceContainer {

	/**
	 * Definitions the container resolves, keyed by id
	 *
	 * @var array<string, mixed>
	 */
	private array $definitions;

	/**
	 * Entries the container has resolved, keyed by id
	 *
	 * @var array<string, mixed>
	 */
	private array $resolved = array();

	/**
	 * @param array<string, mixed> $definitions
	 */
	public function __construct( array $definitions = array() ) {
		$this->definitions = $definitions;
	}

	/**
	 * Checks if the container can resolve an id
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function has( string $id ): bool {
		return array_key_exists( $id, $this->resolved )
			|| array_key_exists( $id, $this->definitions )
			|| class_exists( $id );
	}

	/**
	 * Resolves an id and keeps the entry for the rest of the request
	 *
	 * @param string $id
	 *
	 * @return mixed
	 *
	 * @throws \RuntimeException If the id is neither defined nor an existing class.
	 */
	public function get( string $id ) {
		if ( ! array_key_exists( $id, $this->resolved ) ) {
			$this->resolved[ $id ] = $this->resolve( $id );
		}

		return $this->resolved[ $id ];
	}

	/**
	 * Builds the entry of an id
	 *
	 * @param string $id
	 *
	 * @return mixed
	 *
	 * @throws \RuntimeException If the id is neither defined nor an existing class.
	 */
	private function resolve( string $id ) {
		if ( array_key_exists( $id, $this->definitions ) ) {
			$definition = $this->definitions[ $id ];

			return $definition instanceof \Closure ? $definition( $this ) : $definition;
		}

		if ( ! class_exists( $id ) ) {
			throw new \RuntimeException( esc_html( sprintf( 'The container has no entry for "%s".', $id ) ) );
		}

		return new $id();
	}
}
