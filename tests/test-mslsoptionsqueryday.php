<?php
/**
 * Tests for MslsOptionsQueryDay
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsOptionsQueryDay
 */
class WP_Test_MslsOptionsQueryDay extends Msls_UnitTestCase {

	/**
	 * Verify the has_value-method
	 */
	function test_has_value_method() {
		$obj = new MslsOptionsQueryDay();
		$this->assertInternalType( 'boolean', $obj->has_value( 'de_DE' ) );
		return $obj;
	}

	/**
	 * Verify the get_current_link-method
	 * @depends test_has_value_method
	 */
	function test_get_current_link_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

}
