<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsFields;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsTaxonomy;

final class TestMslsTaxonomy extends MslsUnitTestCase {

	private function MslsTaxonomyFactory( bool $exluded = false ): MslsTaxonomy {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( $exluded );

		Functions\expect( 'msls_options' )->zeroOrMoreTimes()->andReturn( $options );
		Functions\expect( 'apply_filters' )->atLeast()->once();
		Functions\expect( 'get_taxonomies' )->atLeast()->once()->andReturn( array() );

		return new MslsTaxonomy();
	}

	public function test_acl_request_included(): void {
		$cap               = new \stdClass();
		$cap->manage_terms = 'manage_categories';
		$taxonomy          = new \stdClass();
		$taxonomy->cap     = $cap;

		Functions\when( 'get_taxonomy' )->justReturn( $taxonomy );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'get_query_var' )->twice()->with( 'taxonomy' )->andReturn( 'category' );

		$this->assertEquals( 'category', $this->MslsTaxonomyFactory()->acl_request() );
	}

	public function test_acl_request_excluded(): void {
		Functions\expect( 'get_query_var' )->once()->with( 'taxonomy' )->andReturn( 'category' );
		$this->assertEquals( '', $this->MslsTaxonomyFactory( true )->acl_request() );
	}

	public function test_get_post_type(): void {
		Functions\expect( 'get_query_var' )->once()->with( 'taxonomy' )->andReturn( 'category' );
		$this->assertEquals( '', $this->MslsTaxonomyFactory()->get_post_type() );
	}

	public function test_is_post_type(): void {
		Functions\expect( 'get_query_var' )->once()->with( 'taxonomy' )->andReturn( 'category' );
		$this->assertFalse( $this->MslsTaxonomyFactory()->is_post_type() );
	}

	public function test_is_taxonomy(): void {
		Functions\expect( 'get_query_var' )->once()->with( 'taxonomy' )->andReturn( 'category' );
		$this->assertTrue( $this->MslsTaxonomyFactory()->is_taxonomy() );
	}

	public function test_get_request_empty(): void {
		Functions\expect( 'get_query_var' )->twice()->with( 'taxonomy' )->andReturn( 'category' );
		Functions\expect( 'filter_has_var' )->twice()->with( INPUT_GET, MslsFields::FIELD_TAXONOMY )->andReturn( false );

		$this->assertEquals( 'category', $this->MslsTaxonomyFactory()->get_request() );
	}

	public function test_get_request_not_empty(): void {
		$taxonomy = 'a_random_taxonomy';

		Functions\expect( 'filter_has_var' )->twice()->with( INPUT_GET, MslsFields::FIELD_TAXONOMY )->andReturn( true );
		Functions\expect( 'filter_input' )->twice()->with( INPUT_GET, MslsFields::FIELD_TAXONOMY, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( $taxonomy );

		$this->assertEquals( $taxonomy, $this->MslsTaxonomyFactory()->get_request() );
	}
}
