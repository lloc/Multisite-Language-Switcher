<?php

namespace lloc\Msls\Query;

/**
 * Gets the blog_ids of blogs in the network
 *
 * @package Msls
 */
class BlogsInNetworkQuery extends AbstractQuery {

	public function __invoke() {
		$query = $this->sql_cache->prepare(
			"SELECT blog_id FROM {$this->sql_cache->blogs} WHERE blog_id != %d AND site_id = %d",
			$this->sql_cache->blogid,
			$this->sql_cache->siteid
		);

		return $this->sql_cache->get_results( $query );
	}
}
