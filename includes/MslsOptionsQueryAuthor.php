<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\Query\AuthorPostsCounterQuery;

/**
 * MslsOptionsQueryAuthor
 *
 * @package Msls
 */
class MslsOptionsQueryAuthor extends MslsOptionsQuery {

	/**
	 * The author ID for which the posts count is queried.
	 *
	 * @var int
	 */
	protected int $author_id;

	public function __construct( MslsSqlCacher $sql_cache ) {
		parent::__construct( $sql_cache );

		$this->author_id = self::get_params()['author_id'];
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function get_params(): array {
		return array(
			'author_id' => get_queried_object_id(),
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
			$this->arr[ $language ] = ( new AuthorPostsCounterQuery( $this->sql_cache ) )( $this->author_id );
		}

		return (bool) $this->arr[ $language ];
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return get_author_posts_url( $this->author_id );
	}
}
