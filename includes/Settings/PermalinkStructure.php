<?php

namespace lloc\Msls\Settings;

use lloc\Msls\MslsOptions;

class PermalinkStructure extends BlogOption {

	/**
	 * @var string
	 */
	protected $option_name = 'permalink_structure';

	/**
	 * @var mixed
	 */
	protected $default = false;

	/**
	 * @param string $url
	 * @param bool $with_front
	 *
	 * @return string
	 */
	public function get_home_url( string $url, bool $with_front = false ): string {
		if ( ! $this->option ) {
			return $url;
		}

		$count = 1;
		$url   = str_replace( home_url(), '', $url, $count );

		list( $needle, ) = explode( '/%', $this->option, 2 );

		$url = str_replace( $needle, '', $url );
		if ( is_main_site() && $with_front ) {
			$url = "{$needle}{$url}";
		}

		return home_url( $url );
	}

}