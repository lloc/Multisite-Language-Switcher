<?php

namespace lloc\Msls\Query;

/**
 * Gets the posts_ids of posts that have been translated to a specific language
 *
 * @package Msls
 */
class TranslatedPostIdQuery extends AbstractQuery {


	public function __invoke( string $language ) {
		if ( empty( $language ) ) {
			return array();
		}

		$query = $this->sql_cache->prepare(
			"SELECT option_id, option_name FROM {$this->sql_cache->options} WHERE option_name LIKE %s AND option_value LIKE %s",
			'msls_%',
			'%"' . $language . '"%'
		);

		$post_ids = array();
		foreach ( $this->sql_cache->get_results( $query ) as $post ) {
			$post_ids[] = substr( $post->option_name, 5 );
		}

		return $post_ids;
	}
}
