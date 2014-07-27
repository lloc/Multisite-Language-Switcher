<?php
/**
 * Tests for MslsRegistry
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsRegistry
 */
class WP_Test_MslsRegistry extends Msls_UnitTestCase {

	/**
	 * Verify the instance-method
	 * @covers MslsRegistry::instance
	 */
	function test_instance_method() {
		$obj = MslsRegistry::instance();
		$this->assertInstanceOf( 'MslsRegistry', $obj );
		return $obj;
	}

	/**
	 * Verify the set_object- and get_object-method
	 * @covers MslsRegistry::get_object
	 * @covers MslsRegistry::get
	 * @covers MslsRegistry::set_object
	 * @covers MslsRegistry::set
	 * @depends test_instance_method
	 */
	function test_set_method( $obj ) {
		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', 1 );
		$this->assertEquals( 1, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', null );
		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
	}

}
