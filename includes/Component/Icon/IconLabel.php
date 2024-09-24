<?php declare( strict_types = 1 );

namespace lloc\Msls\Component\Icon;

use lloc\Msls\Component\Icon;
use lloc\Msls\MslsPlugin;

/**
 * Class IconLabel
 *
 * @package lloc\Msls\Component
 */
final class IconLabel extends Icon {

	/**
	 * @return string
	 */
	protected function get_include(): string {
		return '';
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	public function get( string $language ): string {
		return '<span>' . implode( '</span><span>', explode( '_', $language ) ) . '</span>';
	}
}
