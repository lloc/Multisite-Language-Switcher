<?php
/**
 * Tests for MslsLinkImageOnly
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsLinkImageOnly
 */
class WP_Test_MslsLinkImageOnly extends Msls_UnitTestCase {

	/**
	 * Verify the static get_description-method
	 */
	function test_get_description_method() {
		$this->assertInternalType( 'string', MslsLinkImageOnly::get_description() );
	}

}
