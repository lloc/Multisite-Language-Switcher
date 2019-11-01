<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsPlugin;
use lloc\Msls\MslsOptions;

class WP_Test_MslsPlugin extends Msls_UnitTestCase {

	function get_test() {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		return new MslsPlugin( $options );
	}

	/**
	 * Verify the static init-method
	 */
	function test_admin_menu_method() {
		defined( 'MSLS_PLUGIN__FILE__' ) || define( 'MSLS_PLUGIN__FILE__', '/wp-content/plugins/multisite-language-switcher/multisite-language-switcher.php' );
		defined( 'MSLS_PLUGIN_VERSION' ) || define( 'MSLS_PLUGIN_VERSION', '2.3.0' );

		Functions\when( 'wp_enqueue_style' )->returnArg();
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );

		$this->assertInternalType( 'boolean', $this->get_test()->admin_menu() );
	}

	/**
	 * Verify the static init_widget-method
	 */
	function test_init_widget_method() {
		Functions\when( 'register_widget' )->justReturn( true );

		$this->assertInternalType( 'boolean', $this->get_test()->init_widget() );
	}

	/**
	 * Verify the static init_i18n_support-method
	 */
	function test_init_i18n_support_method() {
		defined( 'MSLS_PLUGIN_PATH' ) || define( 'MSLS_PLUGIN_PATH', '/wp-content/plugins/multisite-language-switcher' );

		Functions\when( 'load_plugin_textdomain' )->justReturn( true );

		$this->assertInternalType( 'boolean', $this->get_test()->init_i18n_support() );
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
		Functions\when( 'delete_option' )->justReturn( false );

		$this->assertInternalType( 'boolean', $this->get_test()->uninstall() );
	}

	/**
	 * Verify the static cleanup-method
	 */
	function test_cleanup_method() {
		Functions\when( 'delete_option' )->justReturn( false );

		$this->assertInternalType( 'boolean', MslsPlugin::cleanup() );
	}

}
