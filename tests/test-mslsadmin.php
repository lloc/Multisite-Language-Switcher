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
	 * Verify the subsubsub-method
	 * @depends test_init_method
	 */
	function test_subsubsub( $obj ) {
		$this->assertInternalType( 'string', $obj->subsubsub() );
	}

	/**
	 * Verify the register-method
	 * @depends test_init_method
	 */
	function test_register( $obj ) {
		$this->assertEquals( true, $obj->register() );
	}

	/**
	 * Verify the language_section-method
	 * @depends test_init_method
	 */
	function test_language_section( $obj ) {
		$this->assertEquals( true, $obj->language_section() );
	}

	/**
	 * Verify the main_section-method
	 * @depends test_init_method
	 */
	function test_main_section( $obj ) {
		$this->assertEquals( true, $obj->main_section() );
	}

	/**
	 * Verify the advanced_section-method
	 * @depends test_init_method
	 */
	function test_advanced_section( $obj ) {
		$this->assertEquals( true, $obj->advanced_section() );
	}

	/**
	 * Verify the image_url-method
	 * @depends test_init_method
	 */
	function test_image_url( $obj ) {
		$this->expectOutputString( '<input id="image_url" name="msls[image_url]" value="" size="30"/>' );
		$obj->image_url();
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
		$arr = array( 'a', 'b', 'c' );
		$this->assertInternalType( 'string', $obj->render_select( 'test', $arr ) );
	}

	/**
	 * Verify the validate-method
	 * @depends test_init_method
	 */
	function test_validate( $obj ) {
		$arr = array();
		$this->assertEquals( array( 'display' => 0 ), $obj->validate( $arr ) );
		$arr = array( 'image_url' => '/test/', 'display' => '1' );
		$this->assertEquals( array( 'image_url' => '/test' ,'display' => 1 ), $obj->validate( $arr ) );
	}

	/**
	 * Verify the set_blog_language-method
	 * @depends test_init_method
	 */
	function test_set_blog_language( $obj ) {
		$arr = array( 'abc' => true, 'blog_language' => 'it_IT' );
		$this->assertEquals( array( 'abc' => true ), $obj->set_blog_language( $arr ) );
	}

}
