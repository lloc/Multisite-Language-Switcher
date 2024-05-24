<?php

namespace lloc\Msls;

/**
 * MslsOptionsQueryPostType
 *
 * @package Msls
 */
class MslsOptionsQueryPostType extends MslsOptionsQuery {

	protected string $post_type;

	public function __construct( MslsSqlCacher $sql_cache ) {
		parent::__construct( $sql_cache );

		$this->post_type = self::get_params()['post_type'];
	}

	public static function get_params(): array {
		return array(
			'post_type' => get_query_var( 'post_type' ),
		);
	}

	/**
	 * Check if the array has a non-empty item which has $language as a key
	 *
	 * @param string $language
	 *
	 * @return bool
	 */
	public function has_value( string $language ): bool {
		if ( ! isset( $this->arr[ $language ] ) ) {
			$this->arr[ $language ] = get_post_type_object( $this->post_type );
		}

		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link() {
		return (string) get_post_type_archive_link( $this->post_type );
	}
}
