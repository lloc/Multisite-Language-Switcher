<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;

use Brain\Monkey\Functions;

/**
 * WP_Test_MslsBlogCollection
 */
class WP_Test_MslsBlogCollection extends Msls_UnitTestCase {

	function get_test() {
		Functions\expect( 'get_users' )->atLeast()->once()->andReturn( [] );
		Functions\expect( 'get_blogs_of_user' )->atLeast()->once()->andReturn( [] );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );

		return new MslsBlogCollection();
	}

	function test_get_configured_blog_description_not_empty() {
		Functions\expect( 'get_option' )->andReturn( [] );

		$obj = $this->get_test();

		$this->assertEquals( 'Test', $obj->get_configured_blog_description( 0, 'Test' ) );
	}

	function test_get_configured_blog_description_empty() {
		Functions\expect( 'get_blog_option' )->once()->andReturnNull();

		$obj = $this->get_test();

		$this->assertEquals( false, $obj->get_configured_blog_description( 0, false ) );
	}

	function test_get_blogs_of_reference_user() {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'has_value' )->andReturn( true );

		$obj = $this->get_test();

		$this->assertIsArray( $obj->get_blogs_of_reference_user( $options ) );
	}

	function test_get_current_blog_id() {
		$obj = $this->get_test();

		$this->assertIsInt( $obj->get_current_blog_id() );
	}

	function test_has_current_blog() {
		$obj = $this->get_test();

		$this->assertIsBool( $obj->has_current_blog() );
	}

	function test_get_objects() {
		$obj = $this->get_test();

		$this->assertIsArray( $obj->get_objects() );
	}

	function test_get_plugin_active_blogs() {
		$obj = $this->get_test();

		$this->assertIsArray( $obj->get_plugin_active_blogs() );
	}

	function test_get() {
		$obj = $this->get_test();

		$this->assertIsArray( $obj->get() );
	}

	function test_get_filtered() {
		$obj = $this->get_test();

		$this->assertIsArray( $obj->get_filtered() );
	}

	function test_get_users() {
		$obj = $this->get_test();

		$this->assertIsArray( $obj->get_users() );
	}

}
