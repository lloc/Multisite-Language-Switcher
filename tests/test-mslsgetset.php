<?php
/**
 * Tests for MslsGetSet
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsGetSet
 */
class WP_Test_MslsGetSet extends WP_UnitTestCase {

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
	 * Verify the is_empty-method
	 */
	function test_is_empty_method() {
		$obj = new MslsGetSet();

		$this->assertTrue( $obj->is_empty() );
		$obj->temp = 'test';
		$this->assertFalse( $obj->is_empty() );

		return $obj;
	}

	/**
	 * Verify the has_value-method
	 * @depends test_is_empty_method
	 */
	function test_has_value_method( $obj ) {
		$this->assertTrue( $obj->has_value( 'temp' ) );
	}
	
	/**
	 * Verify the get_arr-method
	 * @depends test_is_empty_method
	 */
	function test_get_arr_method( $obj ) {
		$this->assertEquals( array( 'temp' => 'test' ), $obj->get_arr() );
	}

	/**
	 * Verify the reset-method
	 * @depends test_is_empty_method
	 */
	function test_reset_method( $obj ) {
		$obj->reset();
		$this->assertEquals( array(), $obj->get_arr() );
	}

}
