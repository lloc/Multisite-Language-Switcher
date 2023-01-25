<?php

namespace lloc\Msls\Settings;

/**
 * @package Msls
 */
class ActiveSidewidePlugins {

	/**
	 * @var array<string, mixed>
	 */
	protected $option;

	/**
	 * @var string
	 */
	protected $option_name = 'active_sitewide_plugins';

	public function __construct() {
		$this->option = get_site_option( $this->option_name, [] );
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	public function is_active( string $path ): bool {
		return isset( $this->option[ $path ] );
	}

}