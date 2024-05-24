<?php

namespace lloc\Msls;

use lloc\Msls\Query\DatePostsCounterQuery;

/**
 * MslsOptionsQueryDay
 *
 * @package Msls
 */
class MslsOptionsQueryDay extends MslsOptionsQuery {

	protected int $year;

	protected int $monthnum;
	protected int $day;

	public function __construct( MslsSqlCacher $sql_cache ) {
		parent::__construct( $sql_cache );

		$params = self::get_params();

		$this->year     = $params['year'];
		$this->monthnum = $params['monthnum'];
		$this->day      = $params['day'];
	}

	public static function get_params(): array {
		return array(
			'year'     => get_query_var( 'year' ),
			'monthnum' => get_query_var( 'monthnum' ),
			'day'      => get_query_var( 'day' ),
		);
	}

	/**
	 * Check if the array has a non-empty item which has $language as a key
	 *
	 * @param string $language
	 *
	 * @return bool
	 */
	public function has_value( string $language ): bool {
		if ( ! isset( $this->arr[ $language ] ) ) {
			$query_callable = new DatePostsCounterQuery( $this->sql_cache );

			$this->arr[ $language ] = $query_callable( $this->year, $this->monthnum, $this->day );
		}

		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link() {
		return get_day_link( $this->year, $this->monthnum, $this->day );
	}
}
