<?php declare( strict_types=1 );

namespace lloc\Msls\Options;

use lloc\Msls\MslsSqlCacher;

/**
 * OptionsQuery
 *
 * @package Msls
 */
class OptionsQuery extends Options {

	/**
	 * Rewrite with front
	 *
	 * @var bool
	 */
	public ?bool $with_front = true;

	/**
	 * @var MslsSqlCacher
	 */
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
	 * @param int $id This parameter is unused here.
	 *
	 * @return ?OptionsQuery
	 */
	public static function create( $id = 0 ): ?OptionsQuery {
		if ( is_day() ) {
			$query_class = OptionsQueryDay::class;
		} elseif ( is_month() ) {
			$query_class = OptionsQueryMonth::class;
		} elseif ( is_year() ) {
			$query_class = OptionsQueryYear::class;
		} elseif ( is_author() ) {
			$query_class = OptionsQueryAuthor::class;
		} elseif ( is_post_type_archive() ) {
			$query_class = OptionsQueryPostType::class;
		}

		if ( ! isset( $query_class ) ) {
			return null;
		}

		$sql_cache = MslsSqlCacher::create( $query_class, $query_class::get_params() );

		return new $query_class( $sql_cache );
	}

	public function get_permalink( string $language ): string {
		return (string) apply_filters(
			'msls_options_get_permalink',
			$this->get_postlink( $language ),
			$language
		);
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
				$post_link = apply_filters_deprecated( 'check_url', array( $post_link, $this ), '2.7.1', Options::MSLS_GET_POSTLINK_HOOK );

				return apply_filters( Options::MSLS_GET_POSTLINK_HOOK, $post_link, $this );
			}
		}

		return '';
	}
}
