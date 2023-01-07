<?php

namespace lloc\Msls\Settings;

class BlogActivePlugins extends BlogOption {

	/**
	 * @var string
	 */
	protected $option_name = 'active_plugins';

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	public function is_active( string $path ): bool {
		return in_array( $path, $this->option );
	}

}