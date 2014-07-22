<?php
/**
 * Tests for MslsOptions
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsOptions
 */
class WP_Test_MslsAdminIcon extends WP_UnitTestCase {

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
	 * Verify the create-method
	 */
	function test_create_method() {
		$this->assertInstanceOf( 'MslsAdminIcon', MslsOptions::create() );
	}

	/**
	 * Verify the set_path-method
	 * @depends test_create_method
	 */
	function test_set_path( $obj ) {
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_path() );
	}

	/**
	 * Verify the set_language-method
	 * @depends test_create_method
	 */
	function test_set_language( $obj ) {
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_language( 'de_DE' ) );
	}

	/**
	 * Verify the set_src-method
	 * @depends test_create_method
	 */
	function test_set_src( $obj ) {
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_src( '/dev/test' ) );
	}

	/**
	 * Verify the set_href-method
	 * @depends test_create_method
	 */
	function test_set_href( $obj ) {
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_href( '/test' ) );
	}

	/**
	 * Verify the __toString-method
	 * @depends test_create_method
	 */
	function test___toString( $obj ) {
		$this->assertInternalType( 'string', $obj->__toString() );
	}

	/**
	 * Verify the get_img-method
	 * @depends test_create_method
	 */
	function test_get_img( $obj ) {
		$this->assertInternalType( 'string', $obj->get_img() );
	}

	/**
	 * Verify the get_a-method
	 * @depends test_create_method
	 */
	function test_get_a( $obj ) {
		$this->assertInternalType( 'string', $obj->get_a() );
	}

	/**
	 * Verify the get_edit_new-method
	 * @depends test_create_method
	 */
	function test_get_edit_new( $obj ) {
		$this->assertInternalType( 'string', $obj->get_edit_new() );
	}

}