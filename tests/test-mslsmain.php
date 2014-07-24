<?php
/**
 * Tests for MslsMain
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsMain
 */
class WP_Test_MslsMain extends WP_UnitTestCase {

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
	 * @expectedExceptionCode 0
	 */
	function test_init_method() {
		MslsMain::init();
	}

}
