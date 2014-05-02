<?php
/**
 * Tests for MslsTaxonomy
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsTaxonomy
 */
class WP_Test_MslsTaxonomy extends WP_UnitTestCase {

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
		$this->assertInstanceOf( 'MslsTaxonomy', MslsTaxonomy::instance() );
	}

}
