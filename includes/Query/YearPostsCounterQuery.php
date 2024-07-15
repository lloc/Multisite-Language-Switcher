<?php

namespace lloc\Msls\Query;

class YearPostsCounterQuery extends AbstractQuery {


	public function __invoke( int $year ) {
		if ( $year <= 0 ) {
			return 0;
		}

		$query = $this->sql_cache->prepare(
			"SELECT count(ID) FROM {$this->sql_cache->posts} WHERE YEAR(post_date) = %d AND post_status = 'publish'",
			$year
		);

		return (int) $this->sql_cache->get_var( $query );
	}
}
