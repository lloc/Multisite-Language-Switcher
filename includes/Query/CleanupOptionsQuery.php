<?php

namespace lloc\Msls\Query;

/**
 * Cleans up options
 *
 * @package Msls
 */
class CleanupOptionsQuery extends AbstractQuery {

	public function __invoke() {
		$sql = $this->sql_cache->prepare(
			"DELETE FROM {$this->sql_cache->options} WHERE option_name LIKE %s",
			'msls_%'
		);

		return (bool) $this->sql_cache->query( $sql );
	}
}
