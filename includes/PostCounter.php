<?php

namespace lloc\Msls;

class PostCounter {

	/**
	 * @var array<string, mixed>
	 */
	protected $args = [];

	/**
	 * Input is expected to be \WP_Query compatible
	 *
	 * @param array<string, mixed> $args
	 */
	public function __construct( array $args ) {
		$this->args = $args;
	}

	/**
	 * @return int
	 */
	public function get(): int {
		return ( new \WP_Query( $this->args ) )->post_count ?? 0;
	}

}