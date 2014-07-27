<?php
/**
 * Tests for MslsLinkTextOnly
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsLinkTextOnly
 */
class WP_Test_MslsLinkTextOnly extends Msls_UnitTestCase {

	/**
	 * Verify the static get_description-method
	 */
	function test_get_description_method() {
		$this->assertInternalType( 'string', MslsLinkTextOnly::get_description() );
	}

}
