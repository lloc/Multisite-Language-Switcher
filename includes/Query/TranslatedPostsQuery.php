<?php

namespace lloc\Msls\Query;

class TranslatedPostsQuery extends AbstractQuery {


	public function __invoke( string $language ) {
		if ( empty( $language ) ) {
			return array();
		}

		$query = $this->sql_cache->prepare(
			"SELECT option_id, option_name FROM {$this->sql_cache->options} WHERE option_name LIKE %s AND option_value LIKE %s",
			'msls_%',
			'%"' . $language . '"%'
		);

		return $this->sql_cache->get_results( $query );
	}
}
