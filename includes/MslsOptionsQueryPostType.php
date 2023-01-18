<?php

namespace lloc\Msls;

/**
 * OptionsQueryPostType
 *
 * @package Msls
 */
class MslsOptionsQueryPostType extends MslsOptionsQuery {

	/**
	 * Check if the array has a non-empty item which has $language as a key
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_value( string $key ): bool {
		if ( ! isset( $this->arr[ $key ] ) ) {
			$this->arr[ $key ] = get_post_type_object( $this->get_arg( 0, '' ) );
		}
		return (bool) $this->arr[ $key ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return (string) get_post_type_archive_link( $this->get_arg( 0, '' ) );
	}

}
