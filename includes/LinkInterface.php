<?php

namespace lloc\Msls;

use lloc\Msls\Link\Link;

/**
 * Interface for the link types
 *
 * @property string $txt
 * @property string $src
 * @property string $alt
 * @property string $url
 *
 * @package Msls
 * @phpstan-require-extends Link
 */
interface LinkInterface {

	public function __toString(): string;
}
