<?php
/**
 * Tests for MslsAdminIconTaxonomy
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsAdminIconTaxonomy
 */
class WP_Test_MslsAdminIconTaxonomy extends WP_UnitTestCase {

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
	 * Constructor
	 */
	function constructor_method() {
		return new MslsAdminIcon( 'test' );
	}

	/**
	 * Verify the set_path-method
	 * @depends constructor_method
	 */
	function test_set_path( $obj ) {
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_path() );
	}

	/**
	 * Verify the set_href-method
	 * @depends constructor_method
	 */
	function test_set_href( $obj ) {
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_href( 0 ) );
	}

}
