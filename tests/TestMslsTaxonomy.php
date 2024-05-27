<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsPlugin;
use lloc\Msls\MslsTaxonomy;
use lloc\Msls\MslsOptions;

class TestMslsTaxonomy extends MslsUnitTestCase {

	/**
	 * @param bool $exluded
	 *
	 * @return MslsTaxonomy
	 */
	public function get_test( bool $exluded = false ): MslsTaxonomy {
		parent::setUp();

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( $exluded );

		Functions\expect( 'msls_options' )->zeroOrMoreTimes()->andReturn( $options );

		Functions\expect( 'apply_filters' )->atLeast()->once();

		Functions\expect( 'get_taxonomies' )->atLeast()->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->atLeast()->once()->with( 'taxonomy' )->andReturn( 'category' );

		return new MslsTaxonomy();
	}

	public function test_acl_request_included(): void {
		$cap               = new \stdClass();
		$cap->manage_terms = 'manage_categories';
		$taxonomy          = new \stdClass();
		$taxonomy->cap     = $cap;

		Functions\when( 'get_taxonomy' )->justReturn( $taxonomy );
		Functions\when( 'current_user_can' )->justReturn( true );

		$this->assertEquals( 'category', $this->get_test()->acl_request() );
	}

	public function test_acl_request_excluded(): void {
		$this->assertEquals( '', $this->get_test( true )->acl_request() );
	}

	public function test_get_post_type(): void {
		$this->assertEquals( '', $this->get_test()->get_post_type() );
	}

	public function test_is_post_type(): void {
		$this->assertFalse( $this->get_test()->is_post_type() );
	}

	public function test_is_taxonomy(): void {
		$this->assertTrue( $this->get_test()->is_taxonomy() );
	}

	public function test_get_request(): void {
		$this->assertEquals( 'category', $this->get_test()->get_request() );
	}
}
