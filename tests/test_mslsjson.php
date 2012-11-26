<?php
/**
 * Tests for MslsJson
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsJson
 */
class WP_Test_MslsJson extends WP_UnitTestCase {

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
	 * Verify the add- and the get-methods
	 */
	function test_add_get_methods() {
		$obj = new MslsJson();
		$obj->add( 1, 'Test 1' )
			->add( '2', 'Test 2' )
			->add( null, 'Test 3' );
		$this->assertEquals( array( 0 => 'Test 3', 1 => 'Test 1', 2 => 'Test 2' ), $obj->get() );
	}

	/**
	 * Verify the get- and the __toString-methods
	 */
	function test_get_toString_methods() {
		$obj = new MslsJson();
		$obj->add( 1, 'Test 1' )
			->add( '2', 'Test 2' )
			->add( null, 'Test 3' );
		$this->assertEquals( array( 0 => 'Test 3', 1 => 'Test 1', 2 => 'Test 2' ), $obj->__toString() );
	}

}
