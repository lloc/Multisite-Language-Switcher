<?php

namespace lloc\Msls;

/**
 * Interface for the link types
 *
 * @property string $txt
 * @property string $src
 * @property string $alt
 * @property string $url
 *
 * @package Msls
 * @phpstan-require-extends MslsLink
 */
interface LinkInterface {

	public function __toString(): string;
}
