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
	 * Verify the check_url-method
	 */
	function test_object() {
		$obj = new MslsOptionsTaxTermCategory( 0 );
		$this->assertInternalType( 'string', $obj->get_postlink( '' ) );
	}

}
