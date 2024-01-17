<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsPlugin;
use lloc\Msls\MslsOptions;

class WP_Test_MslsPlugin extends Msls_UnitTestCase {

	function test_admin_menu_without_autocomplete(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturnTrue();
		Functions\expect( 'wp_enqueue_style' )->twice();
		Functions\expect( 'plugins_url' )->twice()->andReturn( 'https://lloc.de/wp-content/plugins' );

		$options = \Mockery::mock( MslsOptions::class );

		$test = new MslsPlugin( $options );

		$this->assertFalse( $test->custom_enqueue() );
	}

	function test_admin_menu_with_autocomplete(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturnTrue();
		Functions\expect( 'wp_enqueue_style' )->twice();
		Functions\expect( 'plugins_url' )->times( 3 )->andReturn( 'https://lloc.de/wp-content/plugins' );
		Functions\expect( 'wp_enqueue_script' )->once();

		$options = \Mockery::mock( MslsOptions::class );

		$options->activate_autocomplete = true;

		$test = new MslsPlugin( $options );

		$this->assertTrue( $test->custom_enqueue() );
	}

	function test_admin_menu_admin_bar_not_showing(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturnFalse();

		$options = \Mockery::mock( MslsOptions::class );

		$options->activate_autocomplete = true;

		$test = new MslsPlugin( $options );

		$this->assertFalse( $test->custom_enqueue() );
	}
		function test_init_widget_not_excluded(): void {
		Functions\expect( 'register_widget' )->once();

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturnFalse();

		$test = new MslsPlugin( $options );

		$this->assertTrue( $test->init_widget() );
	}

	function test_init_widget_excluded(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturnTrue();

		$test = new MslsPlugin( $options );

		$this->assertFalse( $test->init_widget() );
	}

	/**
	 * Verify the static init_i18n_support-method
	 */
	function test_init_i18n_support(): void {
		Functions\when( 'load_plugin_textdomain' )->justReturn( true );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		$test = new MslsPlugin( $options );

		$this->assertIsBool( $test->init_i18n_support() );
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

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		$test = new MslsPlugin( $options );

		$this->assertIsBool( $test->uninstall() );
	}

	/**
	 * Verify the static cleanup-method
	 */
	function test_cleanup(): void {
		Functions\when( 'delete_option' )->justReturn( false );

		$this->assertIsBool( MslsPlugin::cleanup() );
	}

}
