<?php
/**
 * Tests for MslsBlog
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsBlog
 */
class WP_Test_MslsAdmin extends WP_UnitTestCase {

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
		$obj = MslsAdmin::init();
		$this->assertInstanceOf( 'MslsAdmin', $obj );
		return $obj;
	}

	/**
	 * Verify the render-method
	 * @depends test_init_method
	 */
	function test_render( $obj ) {
		// render just prints a some text
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
		$this->assertInstanceOf( 'MslsAdmin', $obj->render_checkbox( 'test' ) );
	}

	/**
	 * Verify the render_input-method
	 * @depends test_init_method
	 */
	function test_render_input( $obj ) {
		$this->assertInstanceOf( 'MslsAdmin', $obj->render_input( 'test' ) );
	}

	/**
	 * Verify the render_select-method
	 * @depends test_init_method
	 */
	function test_render_select( $obj ) {
		$this->assertInstanceOf( 'MslsAdmin', $obj->render_select( 'test' ) );
	}

	/**
	 * Verify the validate-method
	 * @depends test_init_method
	 */
	function test_validate( $obj ) {
		$this->assertInternalType( 'array', $obj->validate( array() ) );
	}

}
