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
	 * Verify the get_arr-method
	 */
	function test_get_arr_method() {
		$obj = new MslsGetSet();
		$this->assertEquals( array(), $obj->get_arr() );
		$obj->temp = 'test';
		$this->assertEquals( array( 'temp' => 'test' ), $obj->get_arr() );
		return $obj;
	}

	/**
	 * Verify the is_empty-method
	 * @depends test_get_arr_method
	 */
	function test_is_empty_method() {
		$this->assertTrue( $obj->is_empty() );
		$obj->temp = 'test';
		$this->assertFalse( $obj->is_empty() );
	}

	/**
	 * Verify the reset-method
	 * @depends test_get_arr_method
	 */
	function test_reset_method( $obj ) {
		$obj->temp = 'test';
		$obj->reset();
		$this->assertEquals( array(), $obj->get_arr() );
	}

	/**
	 * Verify the has_value-method
	 * @depends test_get_arr_method
	 */
	function test_has_value_method( $obj ) {
		$this->assertFalse( $obj->has_value( 'temp' ) );
		$obj->temp = 'test';
		$this->assertTrue( $obj->has_value( 'temp' ) );
	}

}
