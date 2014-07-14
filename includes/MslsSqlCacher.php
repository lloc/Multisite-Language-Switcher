<?php
/**
 * MslsSqlCacher
 * @author Dennis Ploetner <re@lloc.de>
 * @since 1.0
 */

/**
 * Wrapper to avoid direct SQL without caching
 * @method array get_var( string $sql )
 * @package Msls
 */
class MslsSqlCacher {

	/**
	 * Database object eg. $wpdb
	 * @var object $db
	 */
	protected $db;

	/**
	 * Name of stored object
	 * @var string $key
	 */
	protected $key;

	/**
	 * Constructor
	 * @param object $db
	 * @param string $caller
	 * @param string $param
	 */
	public function __construct( $db, $caller, $param ) {
		$this->db = $db;
		$this->key = (string) $caller . '_' . (string) $param;
	}

	/**
	 * Call a method of the db-object with the needed args and cache the result
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		$result = wp_cache_get( $this->key );
		if ( false === $result ) {
			$result = call_user_func_array( array( $this->db, $method ), $args );
			wp_cache_set( $this->key, $result );
		} 
		return $result;
	}

}
