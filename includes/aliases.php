<?php
/**
 * Backwards-compatibility aliases for the classes that were restructured
 * from the flat lloc\Msls\Msls* layout into per-concern sub-namespaces.
 *
 * The map and the code creating the aliases live in lloc\Msls\Compat\Aliases;
 * this file only invokes it, so that anything requiring it directly keeps
 * working.
 *
 * @package Msls
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

\lloc\Msls\Compat\Aliases::register();
