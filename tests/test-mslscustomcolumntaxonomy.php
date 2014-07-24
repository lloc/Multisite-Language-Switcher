<?php
/**
 * Tests for MslsCustomColumnTaxonomy
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsCustomColumnTaxonomy
 */
class WP_Test_MslsCustomColumnTaxonomy extends WP_UnitTestCase {

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
	 * Verify the init-method
	 */
	function test_init_method() {
		$this->assertInstanceOf( 'MslsCustomColumnTaxonomy', MslsCustomColumnTaxonomy::init() );
	}

}
