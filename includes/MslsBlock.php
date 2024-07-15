<?php

namespace lloc\Msls;

class MslsBlock {


	protected MslsOptions $options;

	public function __construct( MslsOptions $options ) {
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

		register_block_type( MslsPlugin::plugin_dir_path( 'js/msls-widget-block' ) );

		return true;
	}
}
