<?php

namespace lloc\Msls;

use lloc\Msls\Query\MonthPostsCounterQuery;

/**
 * MslsOptionsQueryMonth
 *
 * @package Msls
 */
class MslsOptionsQueryMonth extends MslsOptionsQuery {

	protected int $year;

	protected int $monthnum;

	public function __construct( MslsSqlCacher $sql_cache ) {
		parent::__construct( $sql_cache );

		$params = self::get_params();

		$this->year     = $params['year'];
		$this->monthnum = $params['monthnum'];
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function get_params(): array {
		return array(
			'year'     => get_query_var( 'year' ),
			'monthnum' => get_query_var( 'monthnum' ),
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
			$this->arr[ $language ] = ( new MonthPostsCounterQuery( $this->sql_cache ) )( $this->year, $this->monthnum );

		}

		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return get_month_link( $this->year, $this->monthnum );
	}
}
