<?php

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
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_value( string $key ): bool {
		if ( ! isset( $this->arr[ $key ] ) ) {
			$args = [
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'author'         => $this->get_author_id(),
			];

			$this->arr[ $key ] = ( new PostQuery( $args ) )->has_posts();
		}

		return (bool) $this->arr[ $key ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return get_author_posts_url( $this->get_author_id() );
	}

	/**
	 * @return int
	 */
	public function get_author_id(): int {
		return $this->get_arg( 0, 0 );
	}

}
