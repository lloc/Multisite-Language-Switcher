<?php declare( strict_types=1 );

namespace lloc\Msls\Frontend;

use lloc\Msls\MslsPlugin;
use lloc\Msls\Options\Options;

class Block {

	/**
	 * The options instance.
	 *
	 * @var Options
	 */
	protected Options $options;

	public function __construct( Options $options ) {
		$this->options = $options;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		$obj = new self( msls_options() );

		if ( function_exists( 'register_block_type' ) ) {
			$obj->register_block();
		}
	}

	/**
	 * Register block and shortcode.
	 */
	public function register_block(): bool {
		if ( $this->options->is_excluded() ) {
			return false;
		}

		register_block_type( MslsPlugin::plugin_dir_path( 'assets/js/msls-widget-block' ) );

		return true;
	}
}
