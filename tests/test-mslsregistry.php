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
class WP_Test_MslsRegistry extends WP_UnitTestCase {

	/**
	 * SetUp initial settings
	 */
	function setUp() {
		parent::setUp();
		wp_cache_flush();
	}

	/**
	 * Break down for next test
	 */
	function tearDown() {
		parent::tearDown();
	}

	/**
	 * Verify the set_object- and get_object-method
	 */
	function test_set_method() {
		$obj = MslsRegistry::instance();
		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', 1 );
		$this->assertEquals( 1, $obj->get_object( 'test_var' ) );
		$obj->set_object( 'test_var', null );
		$this->assertEquals( null, $obj->get_object( 'test_var' ) );
	}

	/**
	 * Verify the reset-method
	 */
	function test_instance_method() {
		$this->assertInstanceOf( 'MslsRegistry',  MslsRegistry::instance() );
	}

}
