<?php

namespace lloc\MslsTests;

/**
 * Minimal stand-in for the WP_List_Table of wp-admin/includes/class-wp-list-table.php,
 * which is not loadable outside a WordPress installation.
 */
#[AllowDynamicProperties]
class WP_List_Table {

	/**
	 * @var array<int, mixed>
	 */
	public $items = array();

	/**
	 * @var array<int, mixed>
	 */
	public $_column_headers = array(); // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * @param array<string, mixed> $args
	 */
	public function __construct( $args = array() ) {
	}

	/**
	 * @return int
	 */
	public function get_pagenum() {
		return 1;
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function set_pagination_args( $args ) {
	}
}
