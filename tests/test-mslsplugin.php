<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;
use lloc\Msls\MslsPlugin;
use lloc\Msls\MslsOptions;

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

	public function test_block_init_excluded() {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$plugin = new MslsPlugin( $options );

		$this->assertFalse( $plugin->block_init() );
	}

	public function test_block_init_not_excluded() {
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );

		Functions\expect( 'wp_register_script' )->once();
		Functions\expect( 'register_block_type' )->once();
		Functions\expect( 'add_shortcode' )->once();

		$this->assertTrue( $this->get_test()->block_init() );
	}

	public function test_content_filter_front_page() {
		Functions\expect( 'is_front_page' )->once()->andReturn( true );

		$this->assertEquals( '', $this->get_test()->content_filter( '' ) );
	}

	public function test_content_filter_not_singular() {
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_singular' )->once()->andReturn( false );

		$this->assertEquals( '', $this->get_test()->content_filter( '' ) );
	}

	public function test_content_filter_singular() {
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'get_current_blog_id' )->andReturn( 1 );
		Functions\expect( 'get_users' )->andReturn( [] );
		Functions\expect( 'get_blogs_of_user' )->andReturn( [] );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( true );

		$plugin = new MslsPlugin( $options );

		$this->assertEquals( '', $plugin->content_filter( '' ) );
	}

	public function test_filter_string_has_filter() {
		Functions\expect( 'get_current_blog_id' )->andReturn( 1 );
		Functions\expect( 'get_users' )->andReturn( [] );
		Functions\expect( 'get_blogs_of_user' )->andReturn( [] );
		Functions\expect( 'has_filter' )->with( 'msls_filter_string' )->andReturn( true );

		$this->get_test()->filter_string();

		$this->assertTrue( Filters\applied( 'msls_filter_string' ) > 0 );
	}

	public function test_filter_string_no_filter() {
		Functions\expect( 'get_current_blog_id' )->andReturn( 1 );
		Functions\expect( 'get_users' )->andReturn( [] );
		Functions\expect( 'get_blogs_of_user' )->andReturn( [] );
		Functions\expect( 'has_filter' )->with( 'msls_filter_string' )->andReturn( false );

		$this->assertEquals( '', $this->get_test()->filter_string() );
	}

	public function test_adminbar() {
		$adminbar = \Mockery::mock( \WP_Admin_Bar::class );

		$this->assertEquals( 0, $this->get_test()->update_adminbar( $adminbar ) );
	}

	public function test_print_alternate_links() {
		Functions\when( 'is_admin' )->justReturn( false );
		Functions\when( 'is_front_page' )->justReturn( true );
		Functions\when( 'get_option' )->justReturn( [] );

		$this->expectOutputString( PHP_EOL );
		$this->get_test()->print_alternate_links();
	}

}
