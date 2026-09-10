<?php declare( strict_types=1 );

namespace lloc\Msls\Options\Query;

use lloc\Msls\Db\SqlCacher;

/**
 * OptionsQueryPostType
 *
 * @package Msls
 */
class PostType extends Query {

	/**
	 * The post type for which the options are queried.
	 *
	 * @var string
	 */
	protected string $post_type;

	public function __construct( SqlCacher $sql_cache ) {
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
	public function get_current_link(): string {
		return (string) get_post_type_archive_link( $this->post_type );
	}

	/**
	 * Gets the number of pages this blog has for the current request
	 *
	 * @param string $language
	 * @param string $context
	 *
	 * @return int
	 */
	public function get_max_pages( string $language, string $context = self::PAGINATION_ARCHIVE ): int {
		if ( self::PAGINATION_ARCHIVE !== $context || ! $this->has_value( $language ) ) {
			return 0;
		}

		return self::posts_to_pages( self::count_published( $this->post_type ) );
	}
}
