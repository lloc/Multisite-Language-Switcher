<?php

namespace lloc\Msls\Query;

/**
 * Gets the number of published posts by a month
 *
 * @package Msls
 */
class MonthPostsCounterQuery extends AbstractQuery {

	public function __invoke( int $year, int $monthnum ): int {
		if ( $year <= 0 || $monthnum <= 0 ) {
			return 0;
		}

		$query = $this->sql_cache->prepare(
			"SELECT count(ID) FROM {$this->sql_cache->posts} WHERE YEAR(post_date) = %d AND MONTH(post_date) = %d AND post_status = 'publish'",
			$year,
			$monthnum
		);

		return (int) $this->sql_cache->get_var( $query );
	}
}
