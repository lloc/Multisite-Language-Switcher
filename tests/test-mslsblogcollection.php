<?php
/**
 * Tests for MslsBlogCollection
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsBlogCollection
 */
class WP_Test_MslsBlogCollection extends WP_UnitTestCase {

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
	 * Verify the instance-method
	 */
	function test_instance_method() {
		$this->assertInstanceOf( 'MslsBlogCollection', MslsBlogCollection::instance() );
	}

}
