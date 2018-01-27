<?php
/**
 * Tests for MslsAdminIcon
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsAdminIcon;

/**
 * WP_Test_MslsAdminIcon
 */
class WP_Test_MslsAdminIcon extends Msls_UnitTestCase {

	/**
	 * Verify the create-method
	 */
	function test_create_method() {
		$user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );
		wp_set_current_user( $user_id );

		$this->assertInstanceOf( 'MslsAdminIcon', MslsAdminIcon::create() );

		$obj = new MslsAdminIcon( 'post' );

		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_path() );
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_language( 'de_DE' ) );
		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_src( '/dev/german_flag.png' ) );

		$this->assertEquals( '<img alt="de_DE" src="/dev/german_flag.png" />', $obj->get_img() );
		$this->assertInternalType( 'string', $obj->get_edit_new() );

		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_href( $post_id ) );
		$value = '<a title="Edit the translation in the de_DE-blog" href="http://example.org/wp-admin/post.php?post=' . $post_id . '&amp;action=edit"><img alt="de_DE" src="/dev/german_flag.png" /></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );

		$this->assertInstanceOf( 'MslsAdminIcon', $obj->set_href( 0 ) );
		$value = '<a title="Create a new translation in the de_DE-blog" href="http://example.org/wp-admin/post-new.php"><img alt="de_DE" src="/dev/german_flag.png" /></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	/**
	 * Test path is built using id and language if available
	 */
	public function test_path_is_built_using_id_and_language_if_available() {
		$post_id = $this->factory->post->create();
		$obj     = new MslsAdminIcon( 'post' );
		$lang    = 'de_DE';
		$obj->set_origin_language( $lang );
		$obj->set_id( $post_id );

		$a = $obj->get_edit_new();

		$query = parse_url( $a, PHP_URL_QUERY );
		parse_str( $query, $query_frags );
		$this->assertArrayHasKey( 'msls_id', $query_frags );
		$this->assertEquals( $post_id, $query_frags['msls_id'] );
		$this->assertArrayHasKey( 'msls_lang', $query_frags );
		$this->assertEquals( $lang, $query_frags['msls_lang'] );
	}
}
