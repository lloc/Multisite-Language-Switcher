<?php
/**
 * Tests for MslsMetaBox
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsMetaBox;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOptionsPost;
use lloc\Msls\MslsRegistry;

/**
 * WP_Test_MslsMetaBox
 */
class WP_Test_MslsMetaBox extends Msls_UnitTestCase {

	public function filter_available_languages( array $available_languages = array() ) {
		$available_languages[] = 'de_DE';

		return $available_languages;
	}

	public function setUp() {
		parent::setUp();
		add_filter( 'get_available_languages', array( $this, 'filter_available_languages' ) );
	}

	/**
	 * Verify the static init-method
	 */
	function test_init_method() {
		$obj = MslsMetaBox::init();
		$this->assertInstanceOf( MslsMetaBox::class, $obj );
		return $obj;
	}

	/**
	 * Test maybe_set_linked_post
	 */
	public function test_maybe_set_linked_post() {
		$post_data = new MslsOptionsPost();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );
		switch_to_blog( $blog_id );
		$post_id = $this->factory->post->create();
		restore_current_blog();

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'de_DE' === $language ? $blog_id : $found;
		}, 10, 2 );

		$_GET['msls_id']   = $post_id;
		$_GET['msls_lang'] = 'de_DE';

		$obj          = MslsMetaBox::init();
		$updated_data = $obj->maybe_set_linked_post( $post_data );

		$this->assertTrue( isset( $updated_data->de_DE ) );
		$this->assertEquals( $post_id, $updated_data->de_DE );
	}

	/**
	 * Test maybe_set_linked_post will not set if id or lang are missing
	 */
	public function test_maybe_set_linked_post_will_not_set_if_id_or_lang_are_missing() {
		$post_data = new MslsOptionsPost();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );
		switch_to_blog( $blog_id );
		$post_id = $this->factory->post->create();
		restore_current_blog();

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'de_DE' === $language ? $blog_id : $found;
		}, 10, 2 );

		$_GET['msls_lang'] = 'de_DE';

		$obj          = MslsMetaBox::init();
		$updated_data = $obj->maybe_set_linked_post( $post_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );

		unset( $_GET['msls_lang'] );
		$_GET['msls_id'] = $post_id;

		$obj          = MslsMetaBox::init();
		$updated_data = $obj->maybe_set_linked_post( $post_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );
	}

	/**
	 * Test maybe_set_linked_post will not set if origin lang is not valid
	 */
	public function test_maybe_set_linked_post_will_not_set_if_origin_lang_is_not_valid() {
		$post_data = new MslsOptionsPost();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );
		switch_to_blog( $blog_id );
		$post_id = $this->factory->post->create();
		restore_current_blog();

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'fr_FR' === $language ? null : $found;
		}, 10, 2 );

		$_GET['msls_lang'] = 'fr_FR';
		$_GET['msls_id']   = $post_id;

		$obj          = MslsMetaBox::init();
		$updated_data = $obj->maybe_set_linked_post( $post_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );
	}

	/**
	 * Test maybe_set_linked_post will not set if origin id is not valid
	 */
	public function test_maybe_set_linked_post_will_not_set_if_origin_id_is_not_valid() {
		$post_data = new MslsOptionsPost();
		$blog_id   = $this->factory->blog->create();
		update_blog_option( get_current_blog_id(), 'WPLANG', 'de_DE' );

		add_filter( 'msls_blog_collection_get_blog_id', function ( $found, $language ) use ( $blog_id ) {
			return 'de_DE' === $language ? $blog_id : $found;
		}, 10, 2 );

		$_GET['msls_id']   = 2323;
		$_GET['msls_lang'] = 'de_DE';

		$obj          = MslsMetaBox::init();
		$updated_data = $obj->maybe_set_linked_post( $post_data );

		$this->assertFalse( isset( $updated_data->de_DE ) );
	}

	/**
	 * Test content import metabox will not be added if option is disabled for blog
	 */
	public function test_content_import_metabox_will_not_be_added_if_option_is_disabled_for_blog() {
		$blog_id = $this->factory->blog->create();
		update_blog_option( $blog_id, 'msls', [] );
		MslsRegistry::set_object( MslsOptions::class, null );

		switch_to_blog( $blog_id );
		$obj = MslsMetaBox::init();
		$obj->add();

		global $wp_meta_boxes;
		$this->assertArrayNotHasKey( 'msls_content_import', $wp_meta_boxes['post']['side']['high'] );
		$this->assertArrayNotHasKey( 'msls_content_import', $wp_meta_boxes['page']['side']['high'] );
	}

	/**
	 * Test content import metabox will be added if option is enabled for blog
	 */
	public function test_content_import_metabox_will_be_added_if_option_is_enabled_for_blog() {
		$blog_id = $this->factory->blog->create();
		update_blog_option( $blog_id, 'msls', [
			'activate_content_import' => '1',
		] );
		MslsRegistry::set_object( MslsOptions::class, null );

		switch_to_blog( $blog_id );
		$obj = MslsMetaBox::init();
		$obj->add();

		global $wp_meta_boxes;
		$this->assertArrayHasKey( 'msls-content-import', $wp_meta_boxes['post']['side']['high'] );
		$this->assertArrayHasKey( 'msls-content-import', $wp_meta_boxes['page']['side']['high'] );
	}

	function tearDown() {
		restore_current_blog();
		parent::tearDown();
	}
}
