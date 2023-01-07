<?php

namespace lloc\Msls\Settings;

class BlogLanguage extends BlogOption {

	/**
	 * @var string
	 */
	protected $option_name = 'WPLANG';

	/**
	 * @var mixed
	 */
	protected $default = '';

	/**
	 * @param string $fallback
	 *
	 * @return string
	 */
	public function get( string $fallback ): string {
		return $this->default !== $this->option ? $this->option : $fallback;
	}

}