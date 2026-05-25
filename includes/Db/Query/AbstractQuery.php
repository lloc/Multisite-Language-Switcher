<?php declare( strict_types=1 );

namespace lloc\Msls\Db\Query;

use lloc\Msls\Db\SqlCacher;

/**
 * AbstractQuery
 *
 * @package Msls
 */
abstract class AbstractQuery {

	/**
	 * The SQL Cacher instance.
	 *
	 * @var SqlCacher
	 */
	protected SqlCacher $sql_cache;

	/**
	 * Constructor.
	 *
	 * @param SqlCacher $sql_cache The SQL Cacher instance.
	 */
	public function __construct( SqlCacher $sql_cache ) {
		$this->sql_cache = $sql_cache;
	}
}
