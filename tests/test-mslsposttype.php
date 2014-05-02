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
class WP_Test_MslsPostType extends WP_UnitTestCase {

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
	 * Verify the reset-method
	 */
	function test_instance_method() {
		$this->assertInstanceOf( 'MslsPostType', MslsPostType::instance() );
	}

}
