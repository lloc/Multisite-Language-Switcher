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
class WP_Test_MslsOptions extends Msls_UnitTestCase {

	/**
	 * Verify the static is_main_page-method
	 */
	function test_is_main_page_method() {
		$this->assertInternalType( 'boolean', MslsOptions::is_main_page() );
	}

	/**
	 * Verify the static is_tax_page-method
	 */
	function test_is_tax_page_method() {
		$this->assertInternalType( 'boolean', MslsOptions::is_tax_page() );
	}

	/**
	 * Verify the static is_query_page-method
	 */
	function test_is_query_page_method() {
		$this->assertInternalType( 'boolean', MslsOptions::is_query_page() );
	}

	/**
	 * Verify the static create-method
	 */
	function test_create_method() {
		$this->assertInstanceOf( 'MslsOptions', MslsOptions::create() );
	}

	/**
	 * Verify the static instance-method
	 * @covers MslsOptions::instance
	 * @covers MslsOptions::__construct
	 */
	function test_instance_method() {
		$obj = MslsOptions::instance();
		$this->assertInstanceOf( 'MslsOptions', $obj );
		return $obj;
	}

	/**
	 * Verify the get_arg-method
	 * @depends test_instance_method
	 */
	function test_get_arg_method( $obj ) {
		$this->assertNull( $obj->get_arg( 0 ) );
		$this->assertInternalType( 'string', $obj->get_arg( 0, '' ) );
		$this->assertInternalType( 'float', $obj->get_arg( 0, 1.1 ) );
		$this->assertInternalType( 'array', $obj->get_arg( 0, array() ) );
	}

	/**
	 * Verify the set-method
	 * @depends test_instance_method
	 */
	function test_set_method( $obj ) {
		$this->assertTrue( $obj->set( array() ) );
		$this->assertTrue( $obj->set( array( 'temp' => 'abc' ) ) );
		$this->assertFalse( $obj->set( 'Test' ) );
		$this->assertFalse( $obj->set( 1 ) );
		$this->assertFalse( $obj->set( 1.1 ) );
		$this->assertFalse( $obj->set( null ) );
		$this->assertFalse( $obj->set( new stdClass() ) );
	}

	/**
	 * Verify the get_permalink-method
	 * @depends test_instance_method
	 */
	function test_get_permalink_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_permalink( 'de_DE' ) );
	}

	/**
	 * Verify the get_postlink-method
	 * @depends test_instance_method
	 */
	function test_get_postlink_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_postlink( 'de_DE' ) );
		$this->assertEquals( '', $obj->get_postlink( 'de_DE' ) );
	}

	/**
	 * Verify the get_current_link-method
	 * @depends test_instance_method
	 */
	function test_get_current_link_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

	/**
	 * Verify the is_excluded-method
	 * @depends test_instance_method
	 */
	function test_is_excluded_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_excluded() );
	}

	/**
	 * Verify the is_content_filter-method
	 * @depends test_instance_method
	 */
	function test_is_content_filter_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_content_filter() );
	}

	/**
	 * Verify the get_order-method
	 * @depends test_instance_method
	 */
	function test_get_order_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_order() );
	}

	/**
	 * Verify the get_url-method
	 * @depends test_instance_method
	 */
	function test_get_url_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_url( '/dev/test' ) );
	}

	/**
	 * Verify the get_flag_url-method
	 * @depends test_instance_method
	 */
	function test_get_flag_url_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_flag_url( 'de_DE' ) );
	}

	/**
	 * Verify the get_available_languages-method
	 * @depends test_instance_method
	 */
	function test_get_available_languages_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get_available_languages() );
	}

}
