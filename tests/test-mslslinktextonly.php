<?php
/**
 * Tests for MslsLinkTextOnly
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsLinkTextOnly
 */
class WP_Test_MslsLinkTextOnly extends WP_UnitTestCase {

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
	 * Verify the static get_description-method
	 */
	function test_get_description_method() {
		$this->assertInternalType( 'string', MslsLinkTextOnly::get_description() );
	}

}
