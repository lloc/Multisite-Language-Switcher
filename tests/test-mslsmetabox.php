<?php
/**
 * Tests for MslsMetaBox
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsMetaBox
 */
class WP_Test_MslsMetaBox extends WP_UnitTestCase {

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
	 * Verify the static init-method
	 */
	function test_init_method() {
		$obj = MslsMetaBox::init();
		$this->assertInstanceOf( 'MslsMetaBox', $obj );
		return $obj;
	}

}
