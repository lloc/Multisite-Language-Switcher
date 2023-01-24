<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\Component\Input\Option;
use lloc\Msls\MslsOptions;
use Mockery\Mock;
use stdClass;

class WP_Test_MslsOptions extends Msls_UnitTestCase {

	public function get_test() {
		Functions\when( 'home_url' )->justReturn( 'https://lloc.de' );
		Functions\when( 'get_option' )->justReturn( [] );
		Functions\when( 'update_option' )->justReturn( true );

		return new MslsOptions();
	}

	public function test_is_main_page(): void {
		Functions\when( 'is_front_page' )->justReturn( true );

		$this->assertIsBool( MslsOptions::is_main_page() );
	}

	public function test_is_tax_page(): void {
		Functions\when( 'is_category' )->justReturn( true );

		$this->assertIsBool( MslsOptions::is_tax_page() );
	}

	public function test_is_query_page(): void {
		Functions\when( 'is_date' )->justReturn( true );

		$this->assertIsBool( MslsOptions::is_query_page() );
	}

	public function test_create(): void {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\when( 'get_post_types' )->justReturn( [] );
		Functions\when( 'get_post_type' )->justReturn( 'post' );
		Functions\when( 'get_option' )->justReturn( [] );

		$this->assertInstanceOf( MslsOptions::class, MslsOptions::create() );
	}

	public function test_get_arg(): void {
		$obj = $this->get_test();

		$this->assertNull( $obj->get_arg( 0 ) );
		$this->assertIsSTring( $obj->get_arg( 0, '' ) );
		$this->assertIsFloat( $obj->get_arg( 0, 1.1 ) );
		$this->assertIsArray( $obj->get_arg( 0, [] ) );
	}

	function set_false_dataprovider() {
		return [
			[ 'Test' ],
			[ 1 ],
			[ 1.1 ],
			[ null ],
			[ (object)[] ],
		];
	}

	/**
	 * @dataProvider set_false_dataprovider
	 */
	function test_set_false(  $condition ): void {
		$this->assertFalse( $this->get_test()->set( $condition ) );
	}

	function set_true_dataprovider() {
		return [
			[ [] ],
			[ [ 'temp' => 'abc' ] ],
			[ [ 'en' => 1, 'de_DE' => 2 ] ],
		];
	}

	/**
	 * @dataProvider set_true_dataprovider
	 */
	function test_set_true( $condition ): void {
		$this->assertTrue( $this->get_test()->set( $condition ) );
	}

	function test_get_permalink(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_permalink( 'de_DE' ) );
	}

	function test_get_postlink(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_postlink( 'de_DE' ) );
		$this->assertEquals( '', $obj->get_postlink( 'de_DE' ) );
	}

	function test_get_current_link(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_current_link() );
	}

	function test_is_excluded(): void {
		$obj = $this->get_test();

		$this->assertIsBool( $obj->is_excluded() );
	}

	function test_is_content_filter(): void {
		$obj = $this->get_test();

		$this->assertIsBool( $obj->is_content_filter() );
	}

	function test_get_order(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_order() );
	}

	function test_get_url(): void {
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );

		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_url( '/dev/test' ) );
	}

	function test_get_flag_url(): void {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );
		Functions\when( 'plugin_dir_path' )->justReturn( __DIR__ . '/../' );

		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_flag_url( 'de_DE' ) );
	}

	function test_get_available_languages(): void {
		Functions\when( 'get_available_languages' )->justReturn( [] );

		$obj = $this->get_test();

		$this->assertIsArray( $obj->get_available_languages() );
	}

	function check_for_blog_dataprovider() {
		return [
			[ '', '' ],
			[ 'https://example.org', 'https://example.org' ],
		];
	}

	/**
	 * @dataProvider check_for_blog_dataprovider
	 */
	function test_check_for_blog_slug( $expected ): void {
		Functions\when( 'is_subdomain_install' )->justReturn( false );

		$options  = \Mockery::mock( Option::class );

		$this->assertEquals( $expected, MslsOptions::check_for_blog_slug( $expected, $options ) );
	}

}
