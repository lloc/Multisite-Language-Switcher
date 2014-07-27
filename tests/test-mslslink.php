<?php
/**
 * Tests for MslsLink
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsLink
 */
class WP_Test_MslsLink extends Msls_UnitTestCase {

	/**
	 * Verify the static get_types-method
	 */
	function test_get_types_method() {
		$this->assertInternalType( 'array', MslsLink::get_types() );
	}

	/**
	 * Verify the static get_description-method
	 */
	function test_get_description_method() {
		$this->assertInternalType( 'string', MslsLink::get_description() );
	}

	/**
	 * Verify the static get_types_description-method
	 */
	function test_get_types_description_method() {
		$this->assertInternalType( 'array', MslsLink::get_types_description() );
	}

	/**
	 * Verify the static callback-method
	 */
	function test_callback_method() {
		$this->assertEquals( '{Test}', MslsLink::callback( 'Test' ) );
	}

	/**
	 * Verify the static create-method
	 */
	function test_create_method() {
		$this->assertInstanceOf( 'MslsLink', MslsLink::create( 1 ) );
		$this->assertInstanceOf( 'MslsLink', MslsLink::create( null ) );
		$obj = MslsLink::create( 0 );
		$this->assertInstanceOf( 'MslsLink', $obj );
		return $obj;
	}

	/**
	 * Verify the __toString-method
	 * @depends test_create_method
	 */
	function test_execute_filter_method( $obj ) {
		$this->assertInternalType( 'string', $obj->__toString() );
	}

}
