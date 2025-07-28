<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * Wrapper to avoid direct SQL without caching
 *
 * @example https://gist.githubusercontent.com/lloc/2c232cef3f910acf692f/raw/91e5fe9ada922a82a32b83eaabad1e2a2ee50338/MslsSqlCacher.php
 *
 * @method mixed get_var( string $sql )
 * @method mixed[] get_results( string $sql )
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
	 * Cache group for the SQL cacher
	 */
	const CACHE_GROUP = 'msls-cache-group';

	/**
	 * @var \wpdb
	 */
	protected \wpdb $db;

	/**
	 * @var string
	 */
	protected string $cache_key;

	/**
	 * Expiration time for the cache in seconds
	 *
	 * @var int
	 */
	protected int $expire;

	/**
	 * Constructor
	 *
	 * @param \wpdb  $db        The WordPress database object.
	 * @param string $cache_key The cache key to use for storing results.
	 * @param int    $expire    The expiration time for the cache in seconds. Default is 0 (no expiration).
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
	 * @param string $name
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
		if ( 'get_' !== substr( $method, 0, 4 ) ) {
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
