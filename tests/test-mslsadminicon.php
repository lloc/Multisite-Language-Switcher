<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsAdminIcon;
use Brain\Monkey\Functions;

class WP_Test_MslsAdminIcon extends Msls_UnitTestCase {

	protected $admin_url = 'https://example.org/wp-admin/';

	protected $lang = 'de_DE';

	protected $src = '/dev/german_flag.png';

	public function get_test( $post ) {
		return ( new MslsAdminIcon( $post->post_type ) )
			->set_path()
			->set_language( $this->lang )
			->set_src( $this->src )
			->set_href( $post->ID );
	}

	public function get_post( $post_type, $id = 0 ) {
		$post = \Mockery::mock( '\WP_Post' );
		$post->ID = $id;
		$post->post_type = $post_type;
		$post->post_author = $this->get_user();

		return $post;
	}

	public function get_user() {
		$user = \Mockery::mock( '\WP_User' );
		$user->ID   = 1;
		$user->role = 'editor';

		return $user;
	}

	public static function get_post_type() {
		return [
			[ 'post', 'http://example.org/wp-admin/post-new.php',                'http://example.org/wp-admin/post.php?post=2&amp;action=edit' ],
			[ 'page', 'http://example.org/wp-admin/post-new.php?post_type=page', 'http://example.org/wp-admin/post.php?post=2&amp;action=edit' ]
		];
	}

	function test_get_a_not_empty_post() {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[0];

		Functions\expect( 'get_edit_post_link' )->once()->andReturn( $edit_link );

		$post = $this->get_post( $post_type, 2 );
		$obj  = $this->get_test( $post );

		$value = '<a title="Edit the translation in the de_DE-blog" href="' . $edit_link . '"><span class="dashicons dashicons-edit"></span></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	function test_get_a_not_empty_page() {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[1];

		Functions\expect( 'add_query_arg' )->twice()->andReturn( $edit_link );
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( $edit_link );

		$post = $this->get_post( $post_type, 2 );
		$obj  = $this->get_test( $post );

		$value = '<a title="Edit the translation in the de_DE-blog" href="' . $edit_link . '"><span class="dashicons dashicons-edit"></span></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_get_a_empty_post() {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[0];

		Functions\expect( 'get_current_blog_id' )->twice()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->twice()->andReturn( $create_link );
		Functions\expect( 'get_edit_post_link' )->twice()->andReturn( null );

		$post = $this->get_post( $post_type );
		$obj  = $this->get_test( $post );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_href( 0 ) );

		$value = sprintf( '<a title="Create a new translation in the de_DE-blog" href="%s"><span class="dashicons dashicons-plus"></span></a>&nbsp;', $create_link );
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_get_a_empty_page() {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[1];

		Functions\expect( 'get_current_blog_id' )->twice()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->twice()->andReturn( $create_link );
		Functions\expect( 'add_query_arg' )->twice()->andReturn( $create_link );
		Functions\expect( 'get_edit_post_link' )->twice()->andReturn( null );

		$post = $this->get_post( $post_type );
		$obj  = $this->get_test( $post );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_href( 0 ) );

		$value = sprintf( '<a title="Create a new translation in the de_DE-blog" href="%s"><span class="dashicons dashicons-plus"></span></a>&nbsp;', $create_link );
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_get_img_post() {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[0];

		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->once()->andReturn( $this->admin_url );
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( $edit_link );

		$post = $this->get_post( $post_type );
		$obj  = $this->get_test( $post );

		$this->assertEquals( '<img alt="de_DE" src="' . $this->src . '" />', $obj->get_img() );
		$this->assertIsSTring( $obj->get_edit_new() );
	}


	public function test_get_img_post_page() {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[1];

		Functions\expect( 'add_query_arg' )->twice()->andReturn( $edit_link );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->once()->andReturn( $this->admin_url );
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( $edit_link );

		$post = $this->get_post( $post_type );
		$obj  = $this->get_test( $post );

		$this->assertEquals( '<img alt="de_DE" src="' . $this->src . '" />', $obj->get_img() );
		$this->assertIsSTring( $obj->get_edit_new() );
	}

	public function test_set_id() {
		$obj  = new MslsAdminIcon( 'post' );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_id( 1 ) );
	}

	public function test_set_origin_language() {
		$obj  = new MslsAdminIcon( 'post' );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_origin_language( 'it_IT' ) );
	}

}
