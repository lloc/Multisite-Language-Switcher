<?php

namespace lloc\Msls\Query;

class DatePostsCounterQuery extends AbstractQuery {


	public function __invoke( int $year, $monthnum, $day ) {
		if ( $year <= 0 || $monthnum <= 0 || $day <= 0 ) {
			return 0;
		}

		$date = ( new \DateTimeImmutable() )->setDate( $year, $monthnum, $day );

		$query = $this->sql_cache->prepare(
			"SELECT count(ID) FROM {$this->sql_cache->posts} WHERE DATE(post_date) = %s AND post_status = 'publish'",
			$date->format( 'Y-m-d' )
		);

		return (int) $this->sql_cache->get_var( $query );
	}
}
