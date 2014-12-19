<?php
/**
 * Tests for MslsOptionsTaxTerm
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsOptionsTaxTerm
 */
class WP_Test_MslsOptionsTaxTerm extends Msls_UnitTestCase {

	/**
	 * Verify the overall functionality
	 */
	function test_object() {
		$obj = new MslsOptionsTaxTerm( 0 );
		$this->assertInternalType( 'string', $obj->get_postlink( '' ) );
	}

}
