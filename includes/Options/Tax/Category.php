<?php declare( strict_types=1 );

namespace lloc\Msls\Options\Tax;

/**
 * OptionsTaxTermCategory
 *
 * @package Msls
 */
class Category extends Term implements OptionsTaxInterface {

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
