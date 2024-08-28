<?php

namespace lloc\Msls;

/**
 * MslsOptionsQuery
 *
 * @package Msls
 */
class MslsOptionsQuery extends MslsOptions {

	/**
	 * Rewrite with front
	 *
	 * @var bool
	 */
	public ?bool $with_front = true;

	protected MslsSqlCacher $sql_cache;

	public function __construct( MslsSqlCacher $sql_cache ) {
		parent::__construct();

		$this->sql_cache = $sql_cache;
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function get_params(): array {
		return array();
	}

	/**
	 * Factory method
	 *
	 * @param int $id This parameter is unused here
	 *
	 * @return ?MslsOptionsQuery
	 */
	public static function create( $id = 0 ): ?MslsOptionsQuery {
		if ( is_day() ) {
			$query_class = MslsOptionsQueryDay::class;
		} elseif ( is_month() ) {
			$query_class = MslsOptionsQueryMonth::class;
		} elseif ( is_year() ) {
			$query_class = MslsOptionsQueryYear::class;
		} elseif ( is_author() ) {
			$query_class = MslsOptionsQueryAuthor::class;
		} elseif ( is_post_type_archive() ) {
			$query_class = MslsOptionsQueryPostType::class;
		}

		if ( ! isset( $query_class ) ) {
			return null;
		}

		$sql_cache = MslsSqlCacher::create( $query_class, $query_class::get_params() );

		return new $query_class( $sql_cache );
	}

	/**
	 * Get postlink
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_postlink( $language ) {
		if ( $this->has_value( $language ) ) {
			$post_link = $this->get_current_link();
			if ( ! empty( $post_link ) ) {
				$post_link = apply_filters_deprecated( 'check_url', array( $post_link, $this ), '2.7.1', 'msls_get_postlink' );

				return apply_filters( 'msls_get_postlink', $post_link, $this );
			}
		}

		return '';
	}
}
