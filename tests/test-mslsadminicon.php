<?php
/**
 * Tests for MslsAdminIcon
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

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

}
