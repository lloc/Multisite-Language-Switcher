<?php
/**
 * Tests for MslsCustomColumnTaxonomy
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsCustomColumnTaxonomy;

/**
 * WP_Test_MslsCustomColumnTaxonomy
 */
class WP_Test_MslsCustomColumnTaxonomy extends Msls_UnitTestCase {

	/**
	 * Verify the init-method
	 */
	function test_init_method() {
		$this->assertInstanceOf( 'MslsCustomColumnTaxonomy', MslsCustomColumnTaxonomy::init() );
	}

}
