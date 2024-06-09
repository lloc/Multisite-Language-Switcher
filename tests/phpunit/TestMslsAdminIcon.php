<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsAdminIcon;

class TestMslsAdminIcon extends MslsUnitTestCase {

	protected string $admin_url = 'https://msls.co/wp-admin/';

	protected string $lang = 'de_DE';

	protected string $src = '/dev/german_flag.png';

	public function get_test( $post ): MslsAdminIcon {
		return ( new MslsAdminIcon( $post->post_type ) )
			->set_path()
			->set_language( $this->lang )
			->set_src( $this->src )
			->set_href( $post->ID );
	}

	public function get_post( $post_type, $id = 0 ): \WP_Post {
		$post              = \Mockery::mock( '\WP_Post' );
		$post->ID          = $id;
		$post->post_type   = $post_type;
		$post->post_author = $this->get_user();

		return $post;
	}

	public function get_user(): \WP_User {
		$user       = \Mockery::mock( '\WP_User' );
		$user->ID   = 1;
		$user->role = 'editor';

		return $user;
	}

	public static function get_post_type(): array {
		return array(
			array(
				'post',
				'http://example.org/wp-admin/post-new.php',
				'http://example.org/wp-admin/post.php?post=2&amp;action=edit',
			),
			array(
				'page',
				'http://example.org/wp-admin/post-new.php?post_type=page',
				'http://example.org/wp-admin/post.php?post=2&amp;action=edit',
			),
		);
	}

	public function test_get_a_not_empty_post(): void {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[0];

		Functions\expect( 'get_edit_post_link' )->once()->andReturn( $edit_link );

		$post = $this->get_post( $post_type, 2 );
		$obj  = $this->get_test( $post );

		$value = '<a title="Edit the translation in the de_DE-blog" href="' . $edit_link . '"><span class="dashicons dashicons-edit"></span></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_get_a_not_empty_page(): void {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[1];

		Functions\expect( 'add_query_arg' )->twice()->andReturn( $edit_link );
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( $edit_link );

		$post = $this->get_post( $post_type, 2 );
		$obj  = $this->get_test( $post );

		$value = '<a title="Edit the translation in the de_DE-blog" href="' . $edit_link . '"><span class="dashicons dashicons-edit"></span></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_get_a_empty_post(): void {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[0];

		Functions\expect( 'get_current_blog_id' )->twice()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->twice()->andReturn( $create_link );
		Functions\expect( 'get_edit_post_link' )->twice()->andReturn( null );

		$post = $this->get_post( $post_type );
		$obj  = $this->get_test( $post );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_href( 0 ) );

		$value = sprintf(
			'<a title="Create a new translation in the de_DE-blog" href="%s"><span class="dashicons dashicons-plus"></span></a>&nbsp;',
			$create_link
		);
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_get_a_empty_page(): void {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[1];

		Functions\expect( 'get_current_blog_id' )->twice()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->twice()->andReturn( $create_link );
		Functions\expect( 'add_query_arg' )->twice()->andReturn( $create_link );
		Functions\expect( 'get_edit_post_link' )->twice()->andReturn( null );

		$post = $this->get_post( $post_type );
		$obj  = $this->get_test( $post );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_href( 0 ) );

		$value = sprintf(
			'<a title="Create a new translation in the de_DE-blog" href="%s"><span class="dashicons dashicons-plus"></span></a>&nbsp;',
			$create_link
		);
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	public function test_get_img_post(): void {
		list ( $post_type, $create_link, $edit_link ) = $this->get_post_type()[0];

		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->once()->andReturn( $this->admin_url );
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( $edit_link );

		$post = $this->get_post( $post_type );
		$obj  = $this->get_test( $post );

		$this->assertEquals( '<img alt="de_DE" src="' . $this->src . '" />', $obj->get_img() );
		$this->assertIsSTring( $obj->get_edit_new() );
	}


	public function test_get_img_post_page(): void {
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

	public function test_set_id_with_null_constructor(): void {
		Functions\expect( 'add_query_arg' )->once();

		$obj = new MslsAdminIcon( null );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_id( 1 ) );
	}

	public function test_set_id(): void {
		$obj = new MslsAdminIcon( 'post' );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_id( 1 ) );
	}

	public function test_set_origin_language(): void {
		$obj = new MslsAdminIcon( 'post' );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_origin_language( 'it_IT' ) );
	}

	public function test_set_icon_type(): void {
		$obj = new MslsAdminIcon( 'post' );

		$this->assertInstanceOf( MslsAdminIcon::class, $obj->set_icon_type( 'flag' ) );
	}

	public function icon_type_provider(): array {
		return array(
			array( 'flag', 'de_DE', '<span class="flag-icon flag-icon-de">de_DE</span>' ),
			array( 'label', 'it_IT', '<span class="language-badge it_IT"><span>it</span><span>IT</span></span>' ),
			array( null, 'fr_FR', '<span class="dashicons dashicons-plus"></span>' ),
			array( null, null, '' ),
		);
	}

	/**
	 * @dataProvider icon_type_provider
	 */
	public function test_get_icon_flag( ?string $icon_type, ?string $language, string $expected ): void {
		Functions\expect( 'plugin_dir_path' )->atLeast( 1 )->andReturn( dirname( __DIR__, 2 ) . '/' );

		$obj = new MslsAdminIcon( 'post' );
		$obj->set_icon_type( $icon_type );

		if ( ! is_null( $language ) ) {
			$obj->set_language( $language );
		}

		$this->assertEquals( $expected, $obj->get_icon() );
	}

	public function test_get_icon_label(): void {
		Functions\expect( 'plugin_dir_path' )->atLeast( 1 )->andReturn( dirname( __DIR__, 2 ) . '/' );

		$obj = new MslsAdminIcon( 'post' );
		$obj->set_icon_type( 'flag' );

		$this->assertEquals( '', $obj->get_icon() );

		$obj->set_language( 'de_DE' );

		$this->assertEquals( '<span class="flag-icon flag-icon-de">de_DE</span>', $obj->get_icon() );
	}

	public function test_get_edit_new(): void {
		$obj = new MslsAdminIcon( 'post' );
		$obj->set_id( 123 );
		$obj->set_origin_language( 'de_DE' );

		Functions\expect( 'add_query_arg' )->once()->andReturn( '' );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->once()->andReturn( 'fake-url' );

		$this->assertEquals( 'fake-url', $obj->get_edit_new() );
	}
}
