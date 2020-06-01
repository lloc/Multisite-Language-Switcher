<?php

namespace lloc\Msls\Component\Icon;

use lloc\Msls\Component\Icon;
use lloc\Msls\MslsPlugin;

/**
 * Class IconSvg
 * @package lloc\Msls\Component
 */
class IconSvg extends Icon {

	/**
	 * @return string
	 */
	protected function get_include(): string {
		return MslsPlugin::plugin_dir_path( 'css-flags/flags.php' );
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

		return $this->maybe( $language, 'flag-icon-' );
	}

}