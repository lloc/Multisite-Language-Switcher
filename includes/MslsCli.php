<?php declare( strict_types=1 );

namespace lloc\Msls;

final class MslsCli {

	/**
	 * Register the WP-CLI command.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	public static function init(): void {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'msls', new self() );
		}
	}

	/**
	 * Get the first blog that has a specific locale set.
	 *
	 * ## OPTIONS
	 *
	 * <locale>
	 * : The locale e.g. de_DE.
	 *
	 * ## EXAMPLES
	 *
	 *  $ wp msls blog <locale>
	 *
	 * @param string[]              $args
	 * @param array<string, string> $assoc_args
	 * @return void
	 */
	public function blog( $args, $assoc_args ): void {
		list( $locale ) = $args;
		$blog           = msls_blog( $locale );

		if ( is_null( $blog ) ) {
			\WP_CLI::error( sprintf( 'No blog with locale %1$s found!', esc_attr( $locale ) ) );
		} else {
			\WP_CLI::success( sprintf( 'Blog ID %1$d has locale %2$s!', $blog->userblog_id, esc_attr( $locale ) ) );
		}
	}
}
