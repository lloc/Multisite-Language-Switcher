<?php
/**
 * MslsOptionsQueryDay
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

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
	 *
	 * @return bool
	 */
	public function has_value( $language ) {
		if ( ! isset( $this->arr[ $language ] ) ) {
			$date = new \DateTime();
			$cache = MslsSqlCacher::init( __CLASS__ )->set_params( $this->args );

			$this->arr[ $language ] = $cache->get_var(
				$cache->prepare(
					"SELECT count(ID) FROM {$cache->posts} WHERE DATE(post_date) = %s AND post_status = 'publish'",
					$date->setDate( $this->get_arg( 0, 0 ),
						$this->get_arg( 1, 0 ),
						$this->get_arg( 2, 0 ) )->format( 'Y-m-d' )
				)
			);
		}

		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link() {
		return get_day_link( $this->get_arg( 0, 0 ), $this->get_arg( 1, 0 ), $this->get_arg( 2, 0 ) );
	}

}
