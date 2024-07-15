<?php

namespace lloc\Msls;

/**
 * Wrapper to avoid direct SQL without caching
 *
 * @example https://gist.githubusercontent.com/lloc/2c232cef3f910acf692f/raw/91e5fe9ada922a82a32b83eaabad1e2a2ee50338/MslsSqlCacher.php
 *
 * @method mixed get_var( string $sql )
 * @method array get_results( string $sql )
 * @method string prepare( string $sql, mixed $a, $b = '', $c = '' )
 * @method mixed query( string $sql )
 * @property string $posts
 * @property string $options
 * @property string $blogs
 * @property int $blogid
 * @property int $siteid
 *
 * @package Msls
 */
class MslsSqlCacher {

	/**
	 * Cache group
	 */
	const CACHE_GROUP = 'msls-cache-group';

	/**
	 * Database object
	 */
	protected \wpdb $db;

	/**
	 * Key for the cached result-set
	 */
	protected string $cache_key;

	/**
	 * Expiration time for the cache in seconds
	 */
	protected int $expire;

	/**
	 * Constructor
	 */
	public function __construct( \wpdb $db, string $cache_key, int $expire = 0 ) {
		$this->db        = $db;
		$this->cache_key = $cache_key;
		$this->expire    = $expire;
	}

	/**
	 * Factory
	 *
	 * @param string $caller
	 * @param mixed  $params
	 * @param int    $expire
	 */
	public static function create( string $caller, $params, int $expire = 0 ): self {
		global $wpdb;

		if ( is_array( $params ) ) {
			$params = implode( '_', $params );
		}

		return new self( $wpdb, esc_attr( $caller . '_' . $params ), $expire );
	}

	/**
	 * Magic __get
	 *
	 * @return mixed
	 */
	public function __get( string $name ) {
		return $this->db->$name ?? null;
	}

	/**
	 * Call a method of the db-object with the needed args and cache the result
	 *
	 * @param string            $method
	 * @param array<int|string> $args
	 *
	 * @return mixed
	 */
	public function __call( string $method, array $args ) {
		if ( 'get_' != substr( $method, 0, 4 ) ) {
			return call_user_func_array( array( $this->db, $method ), $args );
		}

		$result = wp_cache_get( $this->cache_key, self::CACHE_GROUP );
		if ( false === $result ) {
			$result = call_user_func_array( array( $this->db, $method ), $args );
			wp_cache_set( $this->cache_key, $result, self::CACHE_GROUP, $this->expire );
		}

		return $result;
	}
}
