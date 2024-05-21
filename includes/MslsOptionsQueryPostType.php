<?php
/**
 * MslsOptionsQueryPostType
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

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
	 * @param string $language
	 *
	 * @return bool
	 */
	public function has_value( $language ) {
		if ( ! isset( $this->arr[ $language ] ) ) {
			$this->arr[ $language ] = get_post_type_object( $this->get_arg( 0, '' ) );
		}

		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link() {
		return (string) get_post_type_archive_link( $this->get_arg( 0, '' ) );
	}
}
