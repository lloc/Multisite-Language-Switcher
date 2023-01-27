<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;

use Brain\Monkey\Functions;

/**
 * WP_Test_MslsBlogCollection
 */
class WP_Test_MslsBlogCollection extends Msls_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		Functions\stubs( [
			'get_users' => [],
			'get_blogs_of_user' => [],
			'get_current_blog_id' => 1
		] );
	}

	public function test_get_configured_blog_description_not_empty(): void {
		Functions\expect( 'get_option' )->andReturn( [] );

		$this->assertEquals( 'Test', ( new MslsBlogCollection() )->get_configured_blog_description( 0, 'Test' ) );
	}

	public function test_get_configured_blog_description_empty(): void {
		Functions\expect( 'get_blog_option' )->once()->andReturnNull();

		$this->assertEquals( false, ( new MslsBlogCollection() )->get_configured_blog_description( 0, false ) );
	}

	public function test_get_blogs_of_reference_user(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'has_value' )->andReturn( true );

		$this->assertIsArray( ( new MslsBlogCollection() )->get_blogs_of_reference_user( $options ) );
	}

	public function test_get_blog(): void {
		$this->assertNull( ( new MslsBlogCollection() )->get_blog( 'de_DE' ) );
	}

	public function test_get_blog_id(): void {
		$this->assertEquals( 0, ( new MslsBlogCollection() )->get_blog_id( 'de_DE' ) );
	}

	public function test_get_current_blog_id(): void {
		$this->assertIsInt( ( new MslsBlogCollection() )->get_current_blog_id() );
	}

	public function test_is_current_blog_true(): void {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->userblog_id = 1;

		$this->assertTrue( ( new MslsBlogCollection() )->is_current_blog( $blog ) );
	}

	public function test_is_current_blog_false(): void {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->userblog_id = 2;

		$this->assertFalse( ( new MslsBlogCollection() )->is_current_blog( $blog ) );
	}

	public function test_has_current_blog(): void {
		$this->assertIsBool( ( new MslsBlogCollection() )->has_current_blog() );
	}

	public function test_get_objects(): void {
		$this->assertIsArray( ( new MslsBlogCollection() )->get_objects() );
	}

	public function test_get_plugin_active_blogs(): void {
		$this->assertIsArray( ( new MslsBlogCollection() )->get_plugin_active_blogs() );
	}

	public function test_get(): void {
		$this->assertIsArray( ( new MslsBlogCollection() )->get() );
	}

	public function test_get_filtered(): void {
		$this->assertIsArray( ( new MslsBlogCollection() )->get_filtered() );
	}

	public function test_get_users(): void {
		$this->assertIsArray( ( new MslsBlogCollection() )->get_users() );
	}

}
