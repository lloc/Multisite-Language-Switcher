<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptions;

class WP_Test_MslsOptions extends Msls_UnitTestCase {

	public function get_test() {
		Functions\when( 'home_url' )->justReturn( 'https://lloc.de' );
		Functions\when( 'get_option' )->justReturn( [] );
		Functions\when( 'update_option' )->justReturn( true );

		return new MslsOptions();
	}

	public function test_is_main_page_method() {
		Functions\when( 'is_front_page' )->justReturn( true );

		$this->assertInternalType( 'boolean', MslsOptions::is_main_page() );
	}

	public function test_is_tax_page_method() {
		Functions\when( 'is_category' )->justReturn( true );

		$this->assertInternalType( 'boolean', MslsOptions::is_tax_page() );
	}

	public function test_is_query_page_method() {
		Functions\when( 'is_date' )->justReturn( true );

		$this->assertInternalType( 'boolean', MslsOptions::is_query_page() );
	}

	public function test_create_method() {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\when( 'get_post_types' )->justReturn( [] );
		Functions\when( 'get_post_type' )->justReturn( 'post' );
		Functions\when( 'get_option' )->justReturn( [] );

		$this->assertInstanceOf( MslsOptions::class, MslsOptions::create() );
	}

	public function test_get_arg_method() {
		$obj = $this->get_test();

		$this->assertNull( $obj->get_arg( 0 ) );
		$this->assertInternalType( 'string', $obj->get_arg( 0, '' ) );
		$this->assertInternalType( 'float', $obj->get_arg( 0, 1.1 ) );
		$this->assertInternalType( 'array', $obj->get_arg( 0, array() ) );
	}

	function test_set_method() {
		$obj = $this->get_test();

		$this->assertTrue( $obj->set( array() ) );
		$this->assertTrue( $obj->set( array( 'temp' => 'abc' ) ) );
		$this->assertFalse( $obj->set( 'Test' ) );
		$this->assertFalse( $obj->set( 1 ) );
		$this->assertFalse( $obj->set( 1.1 ) );
		$this->assertFalse( $obj->set( null ) );
		$this->assertFalse( $obj->set( new \stdClass() ) );
	}

	function test_get_permalink_method() {
		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->get_permalink( 'de_DE' ) );
	}

	function test_get_postlink_method() {
		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->get_postlink( 'de_DE' ) );
		$this->assertEquals( '', $obj->get_postlink( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

	function test_is_excluded_method() {
		$obj = $this->get_test();

		$this->assertInternalType( 'boolean', $obj->is_excluded() );
	}

	function test_is_content_filter_method() {
		$obj = $this->get_test();

		$this->assertInternalType( 'boolean', $obj->is_content_filter() );
	}

	function test_get_order_method() {
		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->get_order() );
	}

	function test_get_url_method() {
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );

		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->get_url( '/dev/test' ) );
	}

	function test_get_flag_url_method() {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );
		Functions\when( 'plugin_dir_path' )->justReturn( __DIR__ . '/../' );

		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->get_flag_url( 'de_DE' ) );
	}

	function test_get_available_languages_method() {
		Functions\when( 'get_available_languages' )->justReturn( [] );

		$obj = $this->get_test();

		$this->assertInternalType( 'array', $obj->get_available_languages() );
	}

}
