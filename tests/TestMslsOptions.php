<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsAdminIcon;
use lloc\Msls\MslsOptions;

class TestMslsOptions extends MslsUnitTestCase {

	public function get_test(): MslsOptions {
		Functions\when( 'home_url' )->justReturn( 'https://lloc.de' );
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'update_option' )->justReturn( true );

		return new MslsOptions();
	}

	public function test_is_main_page_method(): void {
		Functions\when( 'is_front_page' )->justReturn( true );

		$this->assertIsBool( MslsOptions::is_main_page() );
	}

	public function test_is_tax_page_method(): void {
		Functions\when( 'is_category' )->justReturn( true );

		$this->assertIsBool( MslsOptions::is_tax_page() );
	}

	public function test_is_query_page_method(): void {
		Functions\when( 'is_date' )->justReturn( true );

		$this->assertIsBool( MslsOptions::is_query_page() );
	}

	public function test_create_method(): void {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\when( 'get_post_types' )->justReturn( array() );
		Functions\when( 'get_post_type' )->justReturn( 'post' );
		Functions\when( 'get_option' )->justReturn( array() );

		$this->assertInstanceOf( MslsOptions::class, MslsOptions::create() );
	}

	public function test_get_arg_method(): void {
		$obj = $this->get_test();

		$this->assertNull( $obj->get_arg( 0 ) );
		$this->assertIsSTring( $obj->get_arg( 0, '' ) );
		$this->assertIsFloat( $obj->get_arg( 0, 1.1 ) );
		$this->assertIsArray( $obj->get_arg( 0, array() ) );
	}

	function test_set_method(): void {
		$obj = $this->get_test();

		$this->assertTrue( $obj->set( array() ) );
		$this->assertTrue(
			$obj->set(
				array(
					'temp' => 'abc',
					'en'   => 1,
					'us'   => 2,
				)
			)
		);

		$this->assertFalse( $obj->set( 'Test' ) );
		$this->assertFalse( $obj->set( 1 ) );
		$this->assertFalse( $obj->set( 1.1 ) );
		$this->assertFalse( $obj->set( null ) );
		$this->assertFalse( $obj->set( new \stdClass() ) );
	}

	function test_get_permalink_method(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_permalink( 'de_DE' ) );
	}

	function test_get_postlink_method(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_postlink( 'de_DE' ) );
		$this->assertEquals( '', $obj->get_postlink( 'de_DE' ) );
	}

	function test_get_current_link_method(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_current_link() );
	}

	function test_is_excluded_method(): void {
		$obj = $this->get_test();

		$this->assertIsBool( $obj->is_excluded() );
	}

	function test_is_content_filter_method(): void {
		$obj = $this->get_test();

		$this->assertIsBool( $obj->is_content_filter() );
	}

	function test_get_order_method(): void {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_order() );
	}

	function test_get_url_method(): void {
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );

		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_url( '/dev/test' ) );
	}

	function test_get_flag_url_method(): void {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\when( 'plugins_url' )->justReturn( 'https://lloc.de/wp-content/plugins' );
		Functions\when( 'plugin_dir_path' )->justReturn( __DIR__ . '/../' );

		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_flag_url( 'de_DE' ) );
	}

	function test_get_available_languages_method(): void {
		Functions\expect( 'get_available_languages' )->once()->andReturn( array( 'de_DE', 'it_IT' ) );
		Functions\expect( 'format_code_lang' )->atLeast()->once()->andReturnUsing(
			function ( $code ) {
				$map = array(
					'de_DE' => 'German',
					'it_IT' => 'Italian',
				);
				return $map[ $code ] ?? 'American English';
			}
		);

		$obj = $this->get_test();

		$expected = array(
			'en_US' => 'American English',
			'de_DE' => 'German',
			'it_IT' => 'Italian',
		);
		$this->assertEquals( $expected, $obj->get_available_languages() );
	}

	public function test_get_icon_type_standard(): void {
		$obj = $this->get_test();

		$this->assertEquals( MslsAdminIcon::TYPE_FLAG, $obj->get_icon_type() );
	}

	public function test_get_icon_type_admin_display(): void {
		$obj = $this->get_test();
		$obj->set( array( 'admin_display' => MslsAdminIcon::TYPE_LABEL ) );

		$this->assertEquals( MslsAdminIcon::TYPE_LABEL, $obj->get_icon_type() );
	}
}
