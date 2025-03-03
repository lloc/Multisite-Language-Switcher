<?php

namespace lloc\MslsTests;

class WP_CLI {

	/**
	 * @param string $name
	 * @param mixed  $callable
	 * @param array  $args
	 *
	 * @return bool
	 */
	public static function add_command( $name, $callable, $args = array() ): bool {
		return true;
	}

	/**
	 * @param string $message
	 * @param string $exit
	 */
	public static function error( $message, $exit = true ): void {
		echo $message;
	}

	/**
	 * @param string $message
	 */
	public static function success( $message ): void {
		echo $message;
	}
}
