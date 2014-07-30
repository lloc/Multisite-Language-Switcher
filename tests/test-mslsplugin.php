<?php
/**
 * Tests for MslsPlugin
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsPlugin
 */
class WP_Test_MslsPlugin extends Msls_UnitTestCase {

	/**
	 * Verify the static init-method
	 */
	function test_init_method() {
		$this->assertInternalType( 'boolean', MslsPlugin::init() );
	}

	/**
	 * Verify the static init_widget-method
	 */
	function test_init_widget_method() {
		$this->assertInternalType( 'boolean', MslsPlugin::init_widget() );
	}

	/**
	 * Verify the static init_i18n_support-method
	 */
	function test_init_i18n_support_method() {
		$this->assertInternalType( 'boolean', MslsPlugin::init_i18n_support() );
	}

	/**
	 * Verify the static message_handler-method
	 */
	function test_message_handler_method() {
		$this->expectOutputString( '<div id="msls-warning" class="error"><p>Test</p></div>' );
		MslsPlugin::message_handler( 'Test' );
	}

	/**
	 * Verify the static uninstall-method
	 */
	function test_uninstall_method() {
		$this->assertInternalType( 'boolean', MslsPlugin::uninstall() );
	}

	/**
	 * Verify the static cleanup-method
	 */
	function test_cleanup_method() {
		$this->assertInternalType( 'boolean', MslsPlugin::cleanup() );
	}

}
