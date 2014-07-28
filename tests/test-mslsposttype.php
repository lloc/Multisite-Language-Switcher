<?php
/**
 * Tests for MslsPostType
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsPostType
 */
class WP_Test_MslsPostType extends Msls_UnitTestCase {

	/**
	 * Verify the instance-method
	 * @covers MslsPostType::instance
	 * @covers MslsPostType::__construct
	 */
	function test_instance_method() {
		$obj = MslsPostType::instance();
		$this->assertInstanceOf( 'MslsPostType', $obj );
		return $obj;
	}

	/**
	 * Verify the is_post_type-method
	 * @depends test_instance_method
	 */
	function test_is_post_type_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_post_type() );
		$this->assertTrue( $obj->is_post_type() );
	}

}
