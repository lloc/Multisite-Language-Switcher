<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOutput;
use lloc\Msls\MslsPlugin;
use lloc\Msls\MslsOptions;
use Mockery\Mock;

class WP_Test_MslsPlugin extends Msls_UnitTestCase {

	public function get_test() {
		global $wpdb;

		$wpdb = \Mockery::mock( \WPDB::class );
		$wpdb->shouldReceive( 'prepare' )->andReturnArg( 0 );
		$wpdb->shouldReceive( 'query' )->andReturn( true );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		return new MslsPlugin( $options );
	}

	public function test_admin_menu() {
		Functions\when( 'wp_enqueue_style' )->returnArg();
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );

		$this->assertIsBool( $this->get_test()->admin_menu() );
	}

	public function test_init_widget_true() {
		Functions\when( 'register_widget' )->justReturn( true );

		$this->assertTrue( $this->get_test()->init_widget() );
	}

	public function test_init_widget_false() {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$plugin = new MslsPlugin( $options );

		$this->assertFalse( $plugin->init_widget() );
	}

	public function test_block_render_empty() {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$plugin = new MslsPlugin( $options );

		$this->assertEquals( '', $plugin->block_render() );
	}

	public function test_block_render_output() {
		$expected = 'Booh!';

		Functions\when( 'register_widget' )->justReturn( true );
		Functions\when( 'the_widget' )->justEcho( $expected );

		$this->assertEquals( $expected, $this->get_test()->block_render() );
	}

	public function test_init_i18n_support() {
		Functions\when( 'load_plugin_textdomain' )->justReturn( true );

		$this->assertIsBool( $this->get_test()->init_i18n_support() );
	}

	public function test_message_handler() {
		$this->expectOutputString( '<div id="msls-warning" class="error"><p>Test</p></div>' );
		MslsPlugin::message_handler( 'Test' );
	}

	public function test_uninstall() {
		Functions\when( 'delete_option' )->justReturn( false );

		$this->assertFalse( $this->get_test()->uninstall() );
	}

	public function test_cleanup_true() {
		Functions\when( 'delete_option' )->justReturn( true );

		$this->assertTrue( $this->get_test()->uninstall() );
	}

	public function test_activate() {
		$expected = 'register_unistall_hook called';

		Functions\when( 'register_uninstall_hook' )->justEcho( $expected );

		$this->expectOutputString( $expected );
		MslsPlugin::activate();
	}

	public function test_admin_bar_init_hidden(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturn( false );

		$this->assertFalse( $this->get_test()->admin_bar_init() );
	}

	public function test_admin_bar_init_shown(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturn( true );
		Functions\expect( 'is_super_admin' )->once()->andReturn( true );

		$this->assertTrue( $this->get_test()->admin_bar_init() );
	}

}
