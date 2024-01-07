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
	function test_admin_menu(): void {
		Functions\when( 'wp_enqueue_style' )->returnArg();
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );

		$this->assertIsBool( $this->get_test()->custom_enqueue() );
	}

	/**
	 * Verify the static init_widget-method
	 */
	function test_init_widget(): void {
		Functions\when( 'register_widget' )->justReturn( true );

		$this->assertIsBool( $this->get_test()->init_widget() );
	}

	/**
	 * Verify the static init_i18n_support-method
	 */
	function test_init_i18n_support(): void {
		Functions\when( 'load_plugin_textdomain' )->justReturn( true );

		$this->assertIsBool( $this->get_test()->init_i18n_support() );
	}

	/**
	 * Verify the static message_handler-method
	 */
	function test_message_handler(): void {
		$this->expectOutputString( '<div id="msls-warning" class="error"><p>Test</p></div>' );

		MslsPlugin::message_handler( 'Test' );
	}

	/**
	 * Verify the static uninstall-method
	 */
	function test_uninstall(): void {
		Functions\when( 'delete_option' )->justReturn( false );

		$this->assertIsBool( $this->get_test()->uninstall() );
	}

	/**
	 * Verify the static cleanup-method
	 */
	function test_cleanup(): void {
		Functions\when( 'delete_option' )->justReturn( false );

		$this->assertIsBool( MslsPlugin::cleanup() );
	}

}
