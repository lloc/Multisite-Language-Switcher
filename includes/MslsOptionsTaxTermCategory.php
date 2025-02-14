<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * MslsOptionsTaxTermCategory
 *
 * @package Msls
 */
class MslsOptionsTaxTermCategory extends MslsOptionsTaxTerm implements OptionsTaxInterface {

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
