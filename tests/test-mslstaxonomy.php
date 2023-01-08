<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsPlugin;
use lloc\Msls\MslsTaxonomy;
use lloc\Msls\MslsOptions;
use Mockery;
use stdClass;

class WP_Test_MslsTaxonomy extends Msls_UnitTestCase {

	public function get_test() {
		Functions\when( 'apply_filters' )->returnArg( 2 );
		Functions\when( 'get_option' )->justReturn( [] );

		Functions\expect('get_taxonomies' )->atLeast()->once()->andReturn( [] );
		Functions\expect('get_query_var' )->with( 'taxonomy' )->andReturn( 'category' );

		return new MslsTaxonomy();
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_acl_request_included() {
		$mock = Mockery::mock( 'overload:' . MslsOptions::class );
		$mock->shouldReceive( 'instance' )->andReturnSelf();
		$mock->shouldReceive( 'is_excluded' )->andReturnFalse();

		$cap = new stdClass();
		$cap->manage_terms = 'manage_categories';
		$taxonomy = new stdClass();
		$taxonomy->cap = $cap;

		Functions\when('get_taxonomy' )->justReturn( $taxonomy );
		Functions\when('current_user_can' )->justReturn( true );

		$this->assertEquals( 'category', $this->get_test()->acl_request() );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_acl_request_excluded() {
		$mock = Mockery::mock( 'overload:' . MslsOptions::class );
		$mock->shouldReceive( 'instance' )->andReturnSelf();
		$mock->shouldReceive( 'is_excluded' )->andReturnTrue();

		$this->assertEquals( '', $this->get_test()->acl_request() );
	}
	
	public function test_get_post_type() {
		$this->assertEquals( '', $this->get_test()->get_post_type() );
	}

	public function test_is_post_type() {
		$this->assertFalse( $this->get_test()->is_post_type() );
	}

	public function test_is_taxonomy() {
		$this->assertTrue( $this->get_test()->is_taxonomy() );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_get_request() {
		$plugin = Mockery::mock( 'alias:' . MslsPlugin::class );
		$plugin->shouldReceive( 'get_superglobals' )->andReturn( [ 'taxonomy' => 'abc' ] );

		$this->assertEquals( 'abc', $this->get_test()->get_request() );
	}

}
