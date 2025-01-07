<?php

namespace lloc\Msls;

class MslsCli {

	public static function init(): void {
		\WP_CLI::add_command( 'msls', array( __CLASS__, 'msls' ) );
	}

	public function msls( $args, $assoc_args ): void {
		\WP_CLI::line( 'Hello, World!' );
	}
}
