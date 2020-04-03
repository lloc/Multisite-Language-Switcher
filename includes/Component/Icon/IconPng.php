<?php

namespace lloc\Msls\Component\Icon;

use lloc\Msls\Component\Icon;

/**
 * Class IconPng
 * @package lloc\Msls\Component
 */
class IconPng extends Icon {

	/**
	 * @return string
	 */
	protected function path(): string {
		return plugin_dir_path( MSLS_PLUGIN__FILE__ ) . 'flags/flags.php';
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	public function get( string $language ): string {
		if ( isset( $this->map[ $language ] ) ) {
			return $this->map[ $language ];
		}

		return $this->maybe( $language, '', '.png' );
	}

}