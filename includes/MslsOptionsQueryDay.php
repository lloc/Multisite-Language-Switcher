<?php
/**
 * MslsOptionsQueryDay
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * OptionsQueryDay
 *
 * @package Msls
 */
class MslsOptionsQueryDay extends MslsOptionsQuery {

	/**
	 * Check if the array has an non empty item which has $language as a key
	 *
	 * @param string $language
	 * @return bool
	 */
	public function has_value( $language ) {
		if ( ! isset( $this->arr[ $language ] ) ) {
			global $wpdb;

			$param = DateTime::setDate(
				$this->args[0],
				$this->args[1],
				$this->args[2]
			)->format( 'Y-m-d' );

			$sql = $wpdb->prepare(
				"SELECT count(ID) FROM {$wpdb->posts} WHERE DATE(post_date) = %s AND post_status = 'publish'",
				$param
			);

			$cache = new MslsSqlCacher( $wpdb, __CLASS__, $param );
			$this->arr[ $language ] = $cache->get_var( $sql );
		}
		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link() {
		return get_day_link( $this->args[0], $this->args[1], $this->args[2] );
	}

}
