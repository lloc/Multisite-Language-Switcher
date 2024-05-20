<?php
/**
 * MslsOptionsQuery
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * OptionsQuery
 *
 * @package Msls
 */
class MslsOptionsQuery extends MslsOptions {

	/**
	 * Rewrite with front
	 *
	 * @var bool
	 */
	public $with_front = true;

	/**
	 * Factory method
	 *
	 * @param int $id This parameter is unused here
	 *
	 * @return MslsOptionsQuery|null
	 */
	public static function create( $id = 0 ) {
		$query = null;

		if ( is_day() ) {
			$query = new MslsOptionsQueryDay(
				get_query_var( 'year' ),
				get_query_var( 'monthnum' ),
				get_query_var( 'day' )
			);
		} elseif ( is_month() ) {
			$query = new MslsOptionsQueryMonth(
				get_query_var( 'year' ),
				get_query_var( 'monthnum' )
			);
		} elseif ( is_year() ) {
			$query = new MslsOptionsQueryYear( get_query_var( 'year' ) );
		} elseif ( is_author() ) {
			$query = new MslsOptionsQueryAuthor( get_queried_object_id() );
		} elseif ( is_post_type_archive() ) {
			$query = new MslsOptionsQueryPostType( get_query_var( 'post_type' ) );
		}

		return $query;
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
