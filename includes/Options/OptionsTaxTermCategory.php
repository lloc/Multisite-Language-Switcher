<?php declare( strict_types=1 );

namespace lloc\Msls\Options;

use lloc\Msls\OptionsTaxInterface;

/**
 * OptionsTaxTermCategory
 *
 * @package Msls
 */
class OptionsTaxTermCategory extends OptionsTaxTerm implements OptionsTaxInterface {

	/**
	 * Base option
	 *
	 * @var string
	 */
	const BASE_OPTION = 'category_base';

	/**
	 * Base standard definition
	 *
	 * @var string
	 */
	const BASE_DEFINED = 'category';
}
