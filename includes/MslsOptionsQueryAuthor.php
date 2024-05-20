<?php
/**
 * MslsOptionsQueryAuthor
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * OptionsQueryAuthor
 *
 * @package Msls
 */
class MslsOptionsQueryAuthor extends MslsOptionsQuery {

	/**
	 * Check if the array has a non-empty item which has $language as a key
	 *
	 * @param string $language
	 *
	 * @return bool
	 */
	public function has_value( $language ) {
		if ( ! isset( $this->arr[ $language ] ) ) {
			$cache = MslsSqlCacher::init( __CLASS__ )->set_params( $this->args );

			$this->arr[ $language ] = $cache->get_var(
				$cache->prepare(
					"SELECT count(ID) FROM {$cache->posts} WHERE post_author = %d AND post_status = 'publish'",
					$this->get_arg( 0, 0 )
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
		return get_author_posts_url( $this->get_arg( 0, 0 ) );
	}
}
