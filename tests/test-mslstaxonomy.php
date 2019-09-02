<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsTaxonomy;
use lloc\Msls\MslsOptions;

class WP_Test_MslsTaxonomy extends Msls_UnitTestCase {

	public function get_sut() {
		Functions\when('get_taxonomies' )->justReturn( [] );
		Functions\expect('get_query_var' )->once()->with( 'taxonomy' )->andReturn( 'category' );

		return new MslsTaxonomy();
	}

	public function test_is_taxonomy() {
		$obj = $this->get_sut();

		$this->assertTrue( $obj->is_taxonomy() );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_acl_request_included() {
		$mock = \Mockery::mock( 'overload:' . MslsOptions::class );
		$mock->shouldReceive( 'instance' )->andReturnSelf();
		$mock->shouldReceive( 'is_excluded' )->andReturnFalse();

		$cap = new \stdClass();
		$cap->manage_terms = 'manage_categories';
		$taxonomy = new \stdClass();
		$taxonomy->cap = $cap;

		Functions\when('get_taxonomy' )->justReturn( $taxonomy );
		Functions\when('current_user_can' )->justReturn( true );

		$obj = $this->get_sut();

		$this->assertEquals( 'category', $obj->acl_request() );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_acl_request_excluded() {
		$mock = \Mockery::mock( 'overload:' . MslsOptions::class );
		$mock->shouldReceive( 'instance' )->andReturnSelf();
		$mock->shouldReceive( 'is_excluded' )->andReturnTrue();

		$obj = $this->get_sut();

		$this->assertEquals( '', $obj->acl_request() );
	}
	
	function test_get_post_type() {
		$obj = $this->get_sut();

		$this->assertEquals( '', $obj->get_post_type() );
	}

}
