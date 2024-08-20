<?php

namespace lloc\Msls\Query;

/**
 * Gets the number of published posts by an author
 *
 * @package Msls
 */
class AuthorPostsCounterQuery extends AbstractQuery {

	public function __invoke( int $author_id ): int {
		if ( $author_id <= 0 ) {
			return 0;
		}

		$query = $this->sql_cache->prepare(
			"SELECT count(ID) FROM {$this->sql_cache->posts} WHERE post_author = %d AND post_status = 'publish'",
			$author_id
		);

		return (int) $this->sql_cache->get_var( $query );
	}
}
