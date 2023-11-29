<?php

namespace lloc\Msls\Component\Icon;

use lloc\Msls\Component\Icon;
use lloc\Msls\MslsPlugin;

/**
 * Class IconPng
 * @package lloc\Msls\Component
 */
class IconPng extends Icon {

	const FLAGS_FILE = 'flags/flags.php';

	/**
	 * @return string
	 */
	protected function get_include(): string {
		return MslsPlugin::plugin_dir_path( self::FLAGS_FILE );
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	public function get( string $language ): string {
		return $this->map[ $language ] ?? $this->maybe( $language, '', '.png' );
	}

}