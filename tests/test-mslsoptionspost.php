<?php
/**
 * Tests for MslsOptionsPost
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsOptionsPost
 */
class WP_Test_MslsOptionsPost extends Msls_UnitTestCase {

	/**
	 * Verify the get_postlink-method
	 */
	function test_get_postlink_method() {
		$obj = new MslsOptionsPost();
		$this->assertInternalType( 'string', $obj->get_postlink( 'de_DE' ) );
		return $obj;
	}

	/**
	 * Verify the get_current_link-method
	 * @depends test_get_postlink_method
	 */
	function test_get_current_link_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

}
