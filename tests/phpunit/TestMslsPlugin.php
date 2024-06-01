<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsPlugin;

class TestMslsPlugin extends MslsUnitTestCase {

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

	public function test_print_alternate_links() {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->twice()->andReturn( array() );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->twice()->andReturn( $collection );
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->once()->andReturn( true );
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$this->expectOutputString( '' . PHP_EOL );

		MslsPlugin::print_alternate_links();
	}

	protected function provide_content_filter_data(): array {
		return array(
			array( 'Test', 'Test', true, false, false ),
			array( 'Test', 'Test', false, false, false ),
			array( 'Test', 'Test', false, true, false ),
			array( 'Test', 'Test', false, false, true ),
			array( 'Test', 'Test', true, true, true ),
		);
	}
	/**
	 * @dataProvider provide_content_filter_data
	 */
	public function test_content_filter_empty( string $content, string $expected, bool $is_front_page, bool $is_singular, bool $is_content_filter ) {
		Functions\when( 'is_front_page' )->justReturn( $is_front_page );
		Functions\when( 'is_singular' )->justReturn( $is_singular );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( $is_content_filter );

		$test = new MslsPlugin( $options );

		$this->assertEquals( $expected, $test->content_filter( $content ) );
	}

	public function test_block_init_excluded() {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->once()->andReturn( true );

		$test = new MslsPlugin( $options );

		$this->assertFalse( $test->block_init() );
	}

	public function test_block_init_not_excluded() {
		Functions\expect( 'register_block_type' )->once();
		Functions\expect( 'add_shortcode' )->once();
		Functions\expect( 'plugin_dir_path' )->once();

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->once()->andReturn( false );

		$test = new MslsPlugin( $options );

		$this->assertTrue( $test->block_init() );
	}

	public function test_block_render(): void {
		$expected = '<div id="msls-widget"></div>';

		Functions\expect( 'register_widget' )->once();
		Functions\when( 'the_widget' )->justEcho( $expected );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->once()->andReturn( false );

		$test = new MslsPlugin( $options );

		$this->assertEquals( $expected, $test->block_render() );
	}

	public function test_block_render_exclude(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->once()->andReturn( true );

		$test = new MslsPlugin( $options );

		$this->assertEquals( '', $test->block_render() );
	}

	public function test_activate(): void {
		Functions\expect( 'register_uninstall_hook' )->once();

		MslsPlugin::activate();

		$this->expectOutputString( '' );
	}

	public function test_admin_bar_init_true(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturnTrue();

		$this->assertTrue( MslsPlugin::admin_bar_init() );
	}
	public function test_admin_bar_init_false(): void {
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturn( false );

		$this->assertFalse( MslsPlugin::admin_bar_init() );
	}
}
