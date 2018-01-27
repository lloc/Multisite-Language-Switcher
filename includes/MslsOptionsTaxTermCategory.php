<?php
/**
 * MslsOptionsTaxTermCategory
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Category options
 * @package Msls
 */
class MslsOptionsTaxTermCategory extends MslsOptionsTaxTerm {

	/**
	 * Base option
	 * @var string
	 */
	protected $base_option = 'category_base';

	/**
	 * Base standard definition
	 * @var string
	 */
	protected $base_defined = 'category';

}
