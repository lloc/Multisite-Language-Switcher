<?php
/**
 * MslsSqlCacher
 * @author Dennis Ploetner <re@lloc.de>
 * @since 1.0
 */

/**
 * Wrapper to avoid direct SQL without caching
 * @method mixed get_var( string $sql )
 * @method array get_results( string $sql )
 * @method string prepare( string $sql, mixed $arg,... )
 * @property string $posts
 * @property string $options
 * @package Msls
 */
class MslsSqlCacher {

	/**
	 * Database object
	 * @var object $db
	 */
	protected $db;

	/**
	 * Name of the object which created this object
	 * @var string $caller
	 */
	protected $caller;

	/**
	 * Name of stored object
	 * @var string $key
	 */
	protected $params;

	/**
	 * Constructor
	 * @param object $db
	 * @param string $caller
	 * @param string $param
	 */
	public function __construct( WPDB $db, $caller ) {
		$this->db     = $db;
		$this->caller = $caller;
		$this->params = array();
	}

	/**
	 * Factory
	 * @uses $wpdb
	 * @param string $caller
	 * @return MslsSqlCacher
	 */
	public static function init( $caller ) {
		global $wpdb;
		return new self( $wpdb, $caller );
	}

	/**
	 * Set params
	 * @param mixed $params
	 * @return MslsSqlCacher
	 */
	public function set_params( $params ) {
		$this->params = (array) $params;
		return $this;
	}

	/**
	 * Get the name of the key which is in use for the cached object
	 * @return string
	 */
	public function get_key() {
		return $this->caller . '_' . implode( '_', $this->params );
	}

	/**
	 * Magic __get
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {
		return( isset( $this->db->$key ) ? $this->db->$key : null );
	}

	/**
	 * Call a method of the db-object with the needed args and cache the result
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		if ( 'get_' != substr( $method, 0, 4 ) ) {
			$result = call_user_func_array( array( $this->db, $method ), $args );
		}
		else {
			$key    = $this->get_key();
			$result = wp_cache_get( $key );
			if ( false === $result ) {
				$result = call_user_func_array( array( $this->db, $method ), $args );
				wp_cache_set( $key, $result );
			}
		}
		return $result;
	}

}
