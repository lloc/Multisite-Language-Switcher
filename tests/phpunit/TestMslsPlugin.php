<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOutput;
use lloc\Msls\MslsPlugin;

final class TestMslsPlugin extends MslsUnitTestCase {

	function test_admin_menu_without_autocomplete(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturnTrue();
		Functions\expect( 'wp_enqueue_style' )->twice();
		Functions\expect( 'plugins_url' )->twice()->andReturn( 'https://msls.co/wp-content/plugins' );

		$options = \Mockery::mock( MslsOptions::class );

		$test = new MslsPlugin( $options );

		$this->expectNotToPerformAssertions();
		$test->custom_enqueue();
	}

	function test_admin_menu_with_autocomplete(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturnTrue();
		Functions\expect( 'wp_enqueue_style' )->twice();
		Functions\expect( 'plugins_url' )->times( 3 )->andReturn( 'https://msls.co/wp-content/plugins' );
		Functions\expect( 'wp_enqueue_script' )->once();

		$options = \Mockery::mock( MslsOptions::class );

		$options->activate_autocomplete = true;

		$test = new MslsPlugin( $options );

		$this->expectNotToPerformAssertions();
		$test->custom_enqueue();
	}

	function test_admin_menu_admin_bar_not_showing(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturnFalse();

		$options = \Mockery::mock( MslsOptions::class );

		$options->activate_autocomplete = true;

		$test = new MslsPlugin( $options );

		$this->expectNotToPerformAssertions();
		$test->custom_enqueue();
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
		Functions\expect( 'delete_option' )->times( 3 )->andReturn( false );
		Functions\expect( 'is_multisite' )->once()->andReturn( true );

		global $wpdb;
		$wpdb = \Mockery::mock( '\wpdb' );
		$wpdb->shouldReceive( 'prepare' )->andReturn( '' );
		$wpdb->shouldReceive( 'get_results' )->andReturn( array() );

		$blogs = array(
			(object) array( 'blog_id' => 1 ),
			(object) array( 'blog_id' => 2 ),
		);

		Functions\expect( 'wp_cache_get' )->once()->andReturn( $blogs );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		Functions\expect( 'switch_to_blog' )->times( count( $blogs ) );
		Functions\expect( 'restore_current_blog' )->times( count( $blogs ) );

		$test = new MslsPlugin( $options );

		$this->assertIsBool( $test->uninstall() );
	}

	public function test_cleanup_false(): void {
		Functions\when( 'delete_option' )->justReturn( false );

		$this->assertFalse( MslsPlugin::cleanup() );
	}

	public function test_cleanup_true(): void {
		Functions\when( 'delete_option' )->justReturn( true );

		global $wpdb;
		$wpdb = \Mockery::mock( '\wpdb' );
		$wpdb->shouldReceive( 'prepare' )->andReturn( '' );
		$wpdb->shouldReceive( 'query' )->andReturn( true );

		$this->assertTrue( MslsPlugin::cleanup() );
	}

	public function test_plugin_dir_path(): void {
		Functions\expect( 'plugin_dir_path' )->once()->andReturnUsing(
			function () {
				return trailingslashit( dirname( MSLS_PLUGIN__FILE__ ) );
			}
		);

		$expected = '/var/www/html/wp-content/plugins/multisite-language-switcher/dist/msls-widget-block';
		$this->assertEquals( $expected, MslsPlugin::plugin_dir_path( 'dist/msls-widget-block' ) );
	}

	public function test_print_alternate_links(): void {
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->once()->andReturn( true );
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->twice()->andReturn( array() );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->twice()->andReturn( $collection );
		Functions\expect( 'msls_output' )->once()->andReturn( MslsOutput::create() );

		$this->expectOutputString( '' . PHP_EOL );

		MslsPlugin::print_alternate_links();
	}

	public function test_activate(): void {
		Functions\expect( 'register_uninstall_hook' )->once();

		MslsPlugin::activate();

		$this->expectOutputString( '' );
	}
}
