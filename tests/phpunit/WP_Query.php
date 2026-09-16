<?php

namespace lloc\MslsTests;

/**
 * Minimal stand-in for the WP_Query of wp-includes/class-wp-query.php,
 * which is not loadable outside a WordPress installation.
 */
#[AllowDynamicProperties]
class WP_Query {

	/**
	 * Number of posts the next instance reports, consumed on construction
	 *
	 * @var int
	 */
	public static int $next_found_posts = 0;

	/**
	 * Arguments the last instance was constructed with
	 *
	 * @var array<string, mixed>
	 */
	public static array $last_args = array();

	/**
	 * @var array<int, mixed>
	 */
	public $posts = array();

	/**
	 * @var int
	 */
	public $found_posts = 0;

	/**
	 * @param array<string, mixed> $args
	 */
	public function __construct( $args = array() ) {
		self::$last_args = $args;

		$this->found_posts      = self::$next_found_posts;
		self::$next_found_posts = 0;
	}
}
