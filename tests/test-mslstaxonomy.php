<?php
/**
 * Tests for MslsTaxonomy
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsTaxonomy
 */
class WP_Test_MslsTaxonomy extends Msls_UnitTestCase {

	/**
	 * Verify the instance-method
	 * @covers MslsTaxonomy::instance
	 * @covers MslsTaxonomy::__construct
	 */
	function test_instance_method() {
		$obj = MslsTaxonomy::instance();
		$this->assertInstanceOf( 'MslsTaxonomy', $obj );
		return $obj;
	}

	/**
	 * Verify the is_taxonomy-method
	 * @depends test_instance_method
	 */
	function test_is_taxonomy_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_taxonomy() );
		$this->assertTrue( $obj->is_taxonomy() );
	}

	/**
	 * Verify the acl_request-method
	 * @depends test_instance_method
	 */
	function test_acl_request_method( $obj ) {
		$this->assertInternalType( 'string', $obj->acl_request() );
	}
	
	/**
	 * Verify the get_post_type-method
	 * @depends test_instance_method
	 */
	function test_get_post_type_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_post_type() );
	}

}
