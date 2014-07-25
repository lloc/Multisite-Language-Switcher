<?php
/**
 * Tests for MslsOptionsTaxTermCategory
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsOptionsTaxTermCategory
 */
class WP_Test_MslsOptionsTaxTermCategory extends WP_UnitTestCase {

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
	 * Verify the get_tax_query-method
	 */
	function test_get_base_method() {
		$obj = new MslsOptionsTaxTermCategory( 0 );
		$this->assertInternalType( 'string', $obj->get_base() );
		$this->assertEquals( 'category', $obj->get_base() );
	}
	
}
