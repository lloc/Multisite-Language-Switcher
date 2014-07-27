<?php
/**
 * Tests for MslsAdmin
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsAdmin
 */
class WP_Test_MslsAdmin extends Msls_UnitTestCase {

	/**
	 * Verify the init-method
	 */
	function test_init_method() {
		$obj = MslsAdmin::init();
		$this->assertInstanceOf( 'MslsAdmin', $obj );
		return $obj;
	}

	/**
	 * Verify the init-method
	 * @depends test_init_method
	 */
	function test_subsubsub( $obj ) {
		$this->assertInternalType( 'string', $obj->subsubsub() );
	}

	/**
	 * Verify the render_checkbox-method
	 * @depends test_init_method
	 */
	function test_render_checkbox( $obj ) {
		$this->assertInternalType( 'string', $obj->render_checkbox( 'test' ) );
	}

	/**
	 * Verify the render_input-method
	 * @depends test_init_method
	 */
	function test_render_input( $obj ) {
		$this->assertInternalType( 'string', $obj->render_input( 'test' ) );
	}

	/**
	 * Verify the render_select-method
	 * @depends test_init_method
	 */
	function test_render_select( $obj ) {
		$this->assertInternalType( 'string', $obj->render_select( 'test', array() ) );
	}

	/**
	 * Verify the validate-method
	 * @depends test_init_method
	 */
	function test_validate( $obj ) {
		$this->assertInternalType( 'array', $obj->validate( array() ) );
	}

}
