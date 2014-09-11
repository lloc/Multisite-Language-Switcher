<?php
/**
 * MslsSqlCacher
 * @author Dennis Ploetner <re@lloc.de>
 * @since 1.0
 */

/**
 * Wrapper to avoid direct SQL without caching
 * @example https://gist.githubusercontent.com/lloc/2c232cef3f910acf692f/raw/91e5fe9ada922a82a32b83eaabad1e2a2ee50338/MslsSqlCacher.php
 * @method mixed get_var( string $sql )
 * @method array get_results( string $sql )
 * @method string prepare( string $sql, mixed $a, mixed $b = '', mixed $c = '' )
 * @method mixed query( string $sql )
 * @property string $posts
 * @property string $options
 * @property string $blogs
 * @property int $blogid
 * @property int $siteid
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
	 * Parameters are used to create the key for the cached resultset
	 * @var mixed $params
	 */
	protected $params;

	/**
	 * Constructor
	 * @param object $db
	 * @param string $caller
	 */
	public function __construct( WPDB $db, $caller ) {
		$this->db     = $db;
		$this->caller = $caller;
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
		$this->params = $params;
		return $this;
	}

	/**
	 * Get the name of the key which is in use for the cached object
	 * @return string
	 */
	public function get_key() {
		$params = (
			is_array( $this->params ) ?
			implode( '_', $this->params ) :
			$this->params
		);
		return $this->caller . '_' . $params;
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
