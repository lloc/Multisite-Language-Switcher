<?php

namespace lloc\Msls;

use lloc\Msls\Query\YearPostsCounterQuery;

/**
 * OptionsQueryYear
 *
 * @package Msls
 */
class MslsOptionsQueryYear extends MslsOptionsQuery {

	protected int $year;

	public function __construct( MslsSqlCacher $sql_cache ) {
		parent::__construct( $sql_cache );

		$this->year = self::get_params()['year'];
	}

	public static function get_params(): array {
		return array(
			'year' => get_query_var( 'year' ),
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
			$this->arr[ $language ] = ( new YearPostsCounterQuery( $this->sql_cache ) )( $this->year );
		}

		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return get_year_link( $this->year );
	}
}
