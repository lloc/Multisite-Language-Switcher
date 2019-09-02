<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsAdminIcon;
use Brain\Monkey\Functions;

class WP_Test_MslsAdminIcon extends Msls_UnitTestCase {

	public static function get_post_type() {
		return [
			[ 'post', 'http://example.org/wp-admin/post-new.php' ],
			[ 'page', 'http://example.org/wp-admin/post-new.php?post_type=page' ]
		];
	}

	/**
	 * Verify the class
	 *
	 * @param string $post_type
	 *
	 * @dataProvider get_post_type
	 */
	function test_class( $post_type, $create_link ) {
		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\when( 'get_admin_url' )->justReturn( '' );
		Functions\when( 'add_query_arg' )->returnArg();

		$user = \Mockery::mock( '\WP_User' );
		$user->ID   = 1;
		$user->role = 'editor';

		$post = \Mockery::mock( '\WP_Post' );
		$post->ID = 2;
		$post->post_type = $post_type;
		$post->post_author = $user;

		$obj = new MslsAdminIcon( $post->post_type );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_path() );
		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_language( 'de_DE' ) );
		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_src( '/dev/german_flag.png' ) );

		$this->assertEquals( '<img alt="de_DE" src="/dev/german_flag.png" />', $obj->get_img() );
		$this->assertInternalType( 'string', $obj->get_edit_new() );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_href( $post->ID ) );

		$value = '<a title="Edit the translation in the de_DE-blog" href="http://example.org/wp-admin/post.php?post=' . $post->ID . '&amp;action=edit"><img alt="de_DE" src="/dev/german_flag.png" /></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_href( 0 ) );

		$value = sprintf( '<a title="Create a new translation in the de_DE-blog" href="%s"><img alt="de_DE" src="/dev/german_flag.png" /></a>&nbsp;', $create_link );
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_path_is_built_using_id_and_language_if_available() {
		Functions\when( 'add_query_arg' )->returnArg();

		$post = \Mockery::mock( '\WP_Post' );
		$post->ID = 2;
		$post->post_type = 'post';
		$post->post_author = 'realloc';

		$obj = new MslsAdminIcon( 'post' );
		$lang = 'de_DE';
		$obj->set_origin_language( $lang );
		$obj->set_id( $post );

		$a = $obj->get_edit_new();

		$query = parse_url( $a, PHP_URL_QUERY );
		parse_str( $query, $query_frags );
		$this->assertArrayHasKey( 'msls_id', $query_frags );
		$this->assertEquals( $post, $query_frags['msls_id'] );
		$this->assertArrayHasKey( 'msls_lang', $query_frags );
		$this->assertEquals( $lang, $query_frags['msls_lang'] );
	}
}
