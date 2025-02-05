<?php declare( strict_types=1 );

namespace lloc\Msls;

class MslsCli {

	public static function init(): void {
		\WP_CLI::add_command( 'msls blog', array( __CLASS__, 'blog' ) );
	}

	public function blog( $args, $assoc_args ): void {
		$locale = $args[0] ?? $assoc_args['locale'] ?? null;

		if ( is_null( $locale ) ) {
			\WP_CLI::error( 'Please, provide a locale!' );
			return;
		}
		$blog = msls_blog( $locale );
	}
}
