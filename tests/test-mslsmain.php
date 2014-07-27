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
class WP_Test_MslsMain extends Msls_UnitTestCase {

	/**
	 * Verify the static init-method
	 * @expectedException Exception
	 * @expectedExceptionCode 0
	 */
	function test_init_method() {
		MslsMain::init();
	}

	/**
	 * Verify the get_input_array-method
	 */
	function test_get_input_array_method() {
		$obj = new MslsMain;
		$this->assertInternalType( 'array', $obj->get_input_array( 0 ) );
		return $obj;
	}

	/**
	 * Verify the is_autosave-method
	 * @depends test_get_input_array_method
	 */
	function test_is_autosave_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_autosave( 0 ) );
	}

	/**
	 * Verify the verify_nonce-method
	 * @depends test_get_input_array_method
	 */
	function test_verify_nonce_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->verify_nonce() );
	}

}
