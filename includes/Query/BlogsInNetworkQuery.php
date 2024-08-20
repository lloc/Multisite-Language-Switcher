<?php

namespace lloc\Msls\Query;

/**
 * Gets the blog_ids of blogs in the network
 *
 * @package Msls
 */
class BlogsInNetworkQuery extends AbstractQuery {

	/**
	 * @return int[]
	 */
	public function __invoke(): array {
		$query = $this->sql_cache->prepare(
			"SELECT blog_id FROM {$this->sql_cache->blogs} WHERE blog_id != %d AND site_id = %d",
			$this->sql_cache->blogid,
			$this->sql_cache->siteid
		);

		$blog_ids = array();
		foreach ( $this->sql_cache->get_results( $query ) as $blog ) {
			$blog_ids[] = intval( $blog->blog_id );
		}

		return $blog_ids;
	}
}
