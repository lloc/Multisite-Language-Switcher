<?php
/**
 * Tests for MslsOptionsTax
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsOptionsTax
 */
class WP_Test_MslsOptionsTax extends Msls_UnitTestCase {

	/**
	 * Verify the static create-method
	 */
	function test_create_method() {
		$obj = MslsOptionsTax::create();
		$this->assertInstanceOf( 'MslsOptionsTax', $obj );
		return $obj;
	}

	/**
	 * Verify the get_tax_query-method
	 * @depends test_create_method
	 */
	function test_get_tax_query_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_tax_query() );
	}

	/**
	 * Verify the get_postlink-method
	 * @depends test_create_method
	 */
	function test_get_postlink_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_postlink( 'de_DE' ) );
	}

	/**
	 * Verify the get_current_link-method
	 * @depends test_create_method
	 */
	function test_get_current_link_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

}
