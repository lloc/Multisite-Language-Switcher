<?php
/**
 * Tests for MslsContentTypes
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsContentTypes
 */
class WP_Test_MslsContentTypes extends Msls_UnitTestCase {

	/**
	 * Verify the create-method
	 */
	function test_create_method() {
		$obj = MslsContentTypes::create();
		$this->assertInstanceOf( 'MslsContentTypes', $obj );
		return $obj;
	}

	/**
	 * Verify the is_post_type-method
	 * @depends test_create_method
	 */
	function test_is_post_type( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_post_type() );
	}

	/**
	 * Verify the is_taxonomy-method
	 * @depends test_create_method
	 */
	function test_is_taxonomy( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_taxonomy() );
	}

	/**
	 * Verify the acl_request-method
	 * @depends test_create_method
	 */
	function test_acl_request( $obj ) {
		$this->assertInternalType( 'string', $obj->acl_request() );
	}

	/**
	 * Verify the get-method
	 * @depends test_create_method
	 */
	function test_get( $obj ) {
		$this->assertInternalType( 'array', $obj->get() );
	}

	/**
	 * Verify the get_request-method
	 * @depends test_create_method
	 */
	function test_get_request( $obj ) {
		$this->assertInternalType( 'string', $obj->get_request() );
	}

}
