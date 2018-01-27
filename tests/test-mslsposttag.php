<?php
/**
 * Tests for MslsPostTag
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsPostTag
 */
class WP_Test_MslsPostTag extends Msls_UnitTestCase {

	function get_test() {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();

		return new MslsPostTag( $options, $collection );
	}

	/**
	 * Verify the static suggest-method
	 * @expectedException WPDieException
	 * @expectedExceptionMessage []
	 */
	function test_suggest_method() {
		MslsPostTag::suggest();
	}

	/**
	 * Verify the static the_input-method
	 * @depends get_test
	 */
	function test_the_input_method( $obj ) {
		$tag = new StdClass;
		$tag->term_id = 1;
		$this->assertInternalType( 'boolean', $obj->the_input( $tag, 'test', 'test' ) );
	}

	/**
	 * Test maybe_set_linked_term
	 */
	public function test_maybe_set_linked_term() {
		$term_data = new MslsOptionsTax();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );
		switch_to_blog( $blog_id );
		$term_id = $this->factory->term->create();
		restore_current_blog();

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'de_DE' === $language ? $blog_id : $found;
		}, 10, 2 );

		$_GET['msls_id']   = $term_id;
		$_GET['msls_lang'] = 'de_DE';

		$obj          = $this->get_test();
		$updated_data = $obj->maybe_set_linked_term( $term_data );

		$this->assertTrue( isset( $updated_data->de_DE ) );
		$this->assertEquals( $term_id, $updated_data->de_DE );
	}

	/**
	 * Test maybe_set_linked_term will not set if id or lang are missing
	 */
	public function test_maybe_set_linked_term_will_not_set_if_id_or_lang_are_missing() {
		$term_data = new MslsOptionsTax();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );
		switch_to_blog( $blog_id );
		$term_id = $this->factory->term->create();
		restore_current_blog();

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'de_DE' === $language ? $blog_id : $found;
		}, 10, 2 );

		$_GET['msls_lang'] = 'de_DE';

		$obj          = $this->get_test();
		$updated_data = $obj->maybe_set_linked_term( $term_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );

		unset( $_GET['msls_lang'] );
		$_GET['msls_id'] = $term_id;

		$obj          = $this->get_test();
		$updated_data = $obj->maybe_set_linked_term( $term_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );
	}

	/**
	 * Test maybe_set_linked_term will not set if origin lang is not valid
	 */
	public function test_maybe_set_linked_term_will_not_set_if_origin_lang_is_not_valid() {
		$term_data = new MslsOptionsTax();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );
		switch_to_blog( $blog_id );
		$term_id = $this->factory->term->create();
		restore_current_blog();

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'fr_FR' === $language ? null : $found;
		}, 10, 2 );

		$_GET['msls_lang'] = 'fr_FR';
		$_GET['msls_id']   = $term_id;

		$obj          = $this->get_test();
		$updated_data = $obj->maybe_set_linked_term( $term_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );
	}

	/**
	 * Test maybe_set_linked_term will not set if origin id is not valid
	 */
	public function test_maybe_set_linked_term_will_not_set_if_origin_id_is_not_valid() {
		$term_data = new MslsOptionsTax();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'de_DE' === $language ? $blog_id : $found;
		}, 10, 2 );

		$_GET['msls_id']   = 2323;
		$_GET['msls_lang'] = 'de_DE';

		$obj          = $this->get_test();
		$updated_data = $obj->maybe_set_linked_term( $term_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );
	}

}
