<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Admin\CustomColumnTaxonomy;
use lloc\Msls\ContentTypes\Taxonomy;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;

final class TestCustomColumnTaxonomy extends MslsUnitTestCase {

	public function test_add_hooks_excluded(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$collection = \Mockery::mock( Collection::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$this->expectNotToPerformAssertions();
		CustomColumnTaxonomy::init();
	}

	public function test_add_hooks(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		$collection = \Mockery::mock( Collection::class );

		$taxonomy = \Mockery::mock( Taxonomy::class );
		$taxonomy->shouldReceive( 'get_request' )->andReturn( 'post_tag' );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'msls_taxonomy' )->once()->andReturn( $taxonomy );

		Filters\expectAdded( 'manage_edit-post_tag_columns' )->once();
		Actions\expectAdded( 'manage_post_tag_custom_column' )->once();
		Actions\expectAdded( 'delete_post_tag' )->once();

		$this->expectNotToPerformAssertions();

		CustomColumnTaxonomy::init();
	}

	public function test_th(): void {
		$options = \Mockery::mock( Options::class );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get' )->andReturn( array() )->once();

		$obj = new CustomColumnTaxonomy( $options, $collection );

		$this->assertEmpty( $obj->th( array() ) );
	}

	public function test_column_default(): void {
		$options = \Mockery::mock( Options::class );

		$collection = \Mockery::mock( Collection::class );

		( new CustomColumnTaxonomy( $options, $collection ) )->column_default( '', 'test', 0 );

		$this->expectOutputString( '' );
	}
}
