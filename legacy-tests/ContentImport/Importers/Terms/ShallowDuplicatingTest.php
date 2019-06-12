<?php

namespace lloc\Msls\ContentImport\Importers\Terms;


use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\TestCase;
use lloc\Msls\MslsOptionsTax;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

class ShallowDuplicatingTest extends TestCase {

	/**
	 * It should assign non-hierarchical tax terms to destination post
	 *
	 * @test
	 */
	public function should_assign_non_hierarchical_tax_terms_to_destination_post() {
		/** @var ImportCoordinates $import_coordinates */
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_term_ids = $this->factory()->category->create_many( 3 );
		wp_set_object_terms( $import_coordinates->source_post_id, $source_term_ids, 'category' );
		foreach ( $source_term_ids as $source_term_id ) {
			$relations->should_create( Argument::type( MslsOptionsTax::class ), $import_coordinates->dest_lang, Argument::type( 'int' ) )->will( function ( array $args ) use ( &$source_term_ids ) {
				/** @var MslsOptionsTax $option */
				list( $option, $lang, $id ) = $args;
				$this_source_term_id = $option->get_arg( 0, 23 );
				Assert::assertTrue( in_array( $this_source_term_id, $source_term_ids ) );
				unset( $source_term_ids[ array_search( $this_source_term_id, $source_term_ids, true ) ] );

				return true;
			} );
		}

		restore_current_blog();

		$obj = new ShallowDuplicating( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$mutated = $obj->import( $dest_post_data );

		$this->assertEquals( $dest_post_data, $mutated );

		switch_to_blog($import_coordinates->dest_blog_id);

		$dest_terms = wp_get_object_terms( $import_coordinates->dest_post_id, 'category' );
		$this->assertCount( 3, $dest_terms );
		$this->assertEmpty($source_term_ids);
	}

	/**
	 * It should assign hierarchical tax terms to destination post
	 *
	 * @test
	 */
	public function should_assign_hierarchical_tax_terms_to_destination_post() {
		/** @var ImportCoordinates $import_coordinates */
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_term_ids = $this->factory()->tag->create_many( 3 );
		wp_set_object_terms( $import_coordinates->source_post_id, [], 'category' );
		wp_set_object_terms( $import_coordinates->source_post_id, $source_term_ids, 'post_tag' );
		foreach ( $source_term_ids as $source_term_id ) {
			$relations->should_create( Argument::type( MslsOptionsTax::class ), $import_coordinates->dest_lang, Argument::type( 'int' ) )->will( function ( array $args ) use ( &$source_term_ids ) {
				/** @var MslsOptionsTax $option */
				list( $option, $lang, $id ) = $args;
				$this_source_term_id = $option->get_arg( 0, 23 );
				Assert::assertTrue( in_array( $this_source_term_id, $source_term_ids ) );
				unset( $source_term_ids[ array_search( $this_source_term_id, $source_term_ids, true ) ] );

				return true;
			} );
		}

		restore_current_blog();

		$obj = new ShallowDuplicating( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$mutated = $obj->import( $dest_post_data );

		$this->assertEquals( $dest_post_data, $mutated );

		switch_to_blog($import_coordinates->dest_blog_id);

		$dest_terms = wp_get_object_terms( $import_coordinates->dest_post_id, 'post_tag' );
		$this->assertCount( 3, $dest_terms );
		$this->assertEmpty($source_term_ids);
	}

	/**
	 * It should shallow create hierarchical tax terms
	 *
	 * @test
	 */
	public function should_shallow_create_hierarchical_tax_terms() {
		/** @var ImportCoordinates $import_coordinates */
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();

		switch_to_blog( $import_coordinates->source_blog_id );
		$parent_source_term_id    = $this->factory()->tag->create();
		$children_source_term_ids = $this->factory()->tag->create_many( 3, [ 'parent' => $parent_source_term_id ] );
		wp_set_object_terms( $import_coordinates->source_post_id, [], 'category' );
		wp_set_object_terms( $import_coordinates->source_post_id, $children_source_term_ids, 'post_tag' );
		foreach ( $children_source_term_ids as $source_term_id ) {
			$relations->should_create( Argument::type( MslsOptionsTax::class ), $import_coordinates->dest_lang, Argument::type( 'int' ) )->will( function ( array $args ) use ( &$children_source_term_ids ) {
				/** @var MslsOptionsTax $option */
				list( $option, $lang, $id ) = $args;
				$this_source_term_id = $option->get_arg( 0, 23 );
				Assert::assertTrue( in_array( $this_source_term_id, $children_source_term_ids ) );
				unset( $children_source_term_ids[ array_search( $this_source_term_id, $children_source_term_ids, true ) ] );

				return true;
			} );
		}

		restore_current_blog();

		$obj = new ShallowDuplicating( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$mutated = $obj->import( $dest_post_data );

		$this->assertEquals( $dest_post_data, $mutated );

		switch_to_blog($import_coordinates->dest_blog_id);
		$dest_terms = wp_get_object_terms( $import_coordinates->dest_post_id, 'post_tag' );
		$this->assertCount( 3, $dest_terms );
	}

	/**
	 * It should create a new term in the destination blog if source term is linked to non-existent
	 *
	 * @test
	 */
	public function should_create_a_new_term_in_the_destination_blog_if_source_term_is_linked_to_non_existent() {
		/** @var ImportCoordinates $import_coordinates */
		list( $import_coordinates, $logger, $relations, $dest_post_data ) = $this->setup_source_and_dest();

		switch_to_blog( $import_coordinates->source_blog_id );
		$source_term_id = $this->factory()->category->create();
		wp_set_object_terms( $import_coordinates->source_post_id, $source_term_id, 'category' );
		$option = MslsOptionsTax::create( $source_term_id );
		$option->set( $import_coordinates->dest_lang, 23 );

		$relations->should_create( Argument::type( MslsOptionsTax::class ), $import_coordinates->dest_lang, Argument::type( 'int' ) )->will( function ( array $args ) use ( $source_term_id ) {
			/** @var MslsOptionsTax $option */
			list( $option, $lang, $id ) = $args;
			$this_source_term_id = $option->get_arg( 0, 23 );
			Assert::assertEquals( $source_term_id, $this_source_term_id );

			return true;
		} );

		restore_current_blog();

		$obj = new ShallowDuplicating( $import_coordinates, $logger->reveal(), $relations->reveal() );

		$mutated = $obj->import( $dest_post_data );

		$this->assertEquals( $dest_post_data, $mutated );

		switch_to_blog($import_coordinates->dest_blog_id);

		$dest_terms = wp_get_object_terms( $import_coordinates->dest_post_id, 'category' );
		$this->assertCount( 1, $dest_terms );
		MslsOptionsTax::create($dest_terms[0]->term_id);
	}
}
