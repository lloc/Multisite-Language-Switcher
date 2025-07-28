<?php declare( strict_types=1 );

namespace lloc\Msls\Query;

use lloc\Msls\MslsSqlCacher;

/**
 * AbstractQuery
 *
 * @package Msls
 */
abstract class AbstractQuery {

	/**
	 * The SQL Cacher instance.
	 *
	 * @var MslsSqlCacher
	 */
	protected MslsSqlCacher $sql_cache;

	/**
	 * Constructor.
	 *
	 * @param MslsSqlCacher $sql_cache The SQL Cacher instance.
	 */
	public function __construct( MslsSqlCacher $sql_cache ) {
		$this->sql_cache = $sql_cache;
	}
}
