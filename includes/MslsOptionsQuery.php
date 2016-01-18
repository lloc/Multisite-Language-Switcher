<?php
/**
 * MslsOptionsQuery
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * OptionsQuery
 *
 * @package Msls
 */
class MslsOptionsQuery extends MslsOptions {

	/**
	 * Rewrite with front
	 * @var bool
	 */
	public $with_front = true;

	/**
	 * Factory method
	 *
	 * @param int $id This parameter is unused in this method
	 * @return MslsOptionsQuery|null
	 */
	public static function create( $id = 0 ) {
		if ( is_day() ) {
			return new MslsOptionsQueryDay(
				get_query_var( 'year' ),
				get_query_var( 'monthnum' ),
				get_query_var( 'day' )
			);
		}
		elseif ( is_month() ) {
			return new MslsOptionsQueryMonth(
				get_query_var( 'year' ),
				get_query_var( 'monthnum' )
			);
		}
		elseif ( is_year() ) {
			return new MslsOptionsQueryYear(
				get_query_var( 'year' )
			);
		}
		elseif ( is_author() ) {
			return new MslsOptionsQueryAuthor(
				get_queried_object_id()
			);
		}
		elseif ( is_post_type_archive() ) {
			return new MslsOptionsQueryPostType(
				get_query_var( 'post_type' )
			);
		}
		return null;
	}

	/**
	 * Get postlink
	 *
	 * @param string $language
	 * @return string
	 */
	public function get_postlink( $language ) {
		$url = $this->has_value( $language ) ? $this->get_current_link() : '';

		if ( has_filter( 'check_url' ) ) {
			_deprecated_function( 'check_url( $url, $this )', '1.0.9', 'msls_get_postlink( $url, $this, $language )' );
			$url = apply_filters( 'check_url', $url, $this );
		}

		/** This filter is documented in /includes/MslsOptions.php */
		return apply_filters( 'msls_get_postlink', $url, $this, $language );
	}

}
