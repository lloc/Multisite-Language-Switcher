<?php
/**
 * Tests for MslsOptionsTaxTermCategory
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsOptionsTaxTermCategory
 */
class WP_Test_MslsOptionsTaxTermCategory extends Msls_UnitTestCase {

	/**
	 * Verify the get_tax_query-method
	 */
	function test_get_base_method() {
		$obj = new MslsOptionsTaxTermCategory( 0 );
		$this->assertInternalType( 'string', $obj->get_base() );
		$this->assertEquals( 'category', $obj->get_base() );
	}
	
}
