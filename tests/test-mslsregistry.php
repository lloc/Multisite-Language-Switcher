<?php
/**
 * Tests for MslsRegistry
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsRegistry;

/**
 * WP_Test_MslsRegistry
 */
class WP_Test_MslsRegistry extends Msls_UnitTestCase {

	/**
	 * Verify the instance-method
	 */
	function test_instance_method() {
		$obj = MslsRegistry::instance();
		$this->assertInstanceOf( MslsRegistry::class, $obj );
		return $obj;
	}

	/**
	 * Verify the set_object- and get_object-method
	 *
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
