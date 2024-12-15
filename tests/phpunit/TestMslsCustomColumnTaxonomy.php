<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;
use Brain\Monkey\Actions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomColumnTaxonomy;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsTaxonomy;

final class TestMslsCustomColumnTaxonomy extends MslsUnitTestCase {

	public function test_add_hooks_excluded(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$collection = \Mockery::mock( MslsBlogCollection::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$this->expectNotToPerformAssertions();
		MslsCustomColumnTaxonomy::init();
	}

	public function test_add_hooks(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		$collection = \Mockery::mock( MslsBlogCollection::class );

		$taxonomy = \Mockery::mock( MslsTaxonomy::class );
		$taxonomy->shouldReceive( 'get_request' )->andReturn( 'post_tag' );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'msls_taxonomy' )->once()->andReturn( $taxonomy );

		Filters\expectAdded( 'manage_edit-post_tag_columns' )->once();
		Actions\expectAdded( 'manage_post_tag_custom_column' )->once();
		Actions\expectAdded( 'delete_post_tag' )->once();

		$this->expectNotToPerformAssertions();

		MslsCustomColumnTaxonomy::init();
	}

	public function test_th(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( array() )->once();

		$obj = new MslsCustomColumnTaxonomy( $options, $collection );

		$this->assertEmpty( $obj->th( array() ) );
	}

	public function test_column_default(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );

		( new MslsCustomColumnTaxonomy( $options, $collection ) )->column_default( '', 'test', 0 );

		$this->expectOutputString( '' );
	}
}
