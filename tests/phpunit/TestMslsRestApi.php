<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsRestApi;

final class TestMslsRestApi extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		if ( ! class_exists( \WP_REST_Server::class ) ) {
			// phpcs:ignore
			eval( 'class WP_REST_Server { const CREATABLE = "POST"; }' );
		}

		if ( ! class_exists( \WP_REST_Response::class ) ) {
			// phpcs:ignore
			eval( 'class WP_REST_Response { protected $data; protected $status; public function __construct( $data = null, $status = 200 ) { $this->data = $data; $this->status = $status; } public function get_data() { return $this->data; } public function get_status() { return $this->status; } }' );
		}

		if ( ! class_exists( \WP_Error::class ) ) {
			// phpcs:ignore
			eval( 'class WP_Error { protected $code; protected $message; protected $data; public function __construct( $code = "", $message = "", $data = "" ) { $this->code = $code; $this->message = $message; $this->data = $data; } public function get_error_code() { return $this->code; } }' );
		}
	}

	public function test_check_permission_returns_true(): void {
		$request = \Mockery::mock( \WP_REST_Request::class );
		$request->shouldReceive( 'get_param' )->with( 'source_blog_id' )->andReturn( 1 );
		$request->shouldReceive( 'get_param' )->with( 'source_post_id' )->andReturn( 10 );
		$request->shouldReceive( 'get_param' )->with( 'target_blog_id' )->andReturn( 2 );

		Functions\expect( 'switch_to_blog' )->twice();
		Functions\expect( 'current_user_can' )->once()->with( 'read_post', 10 )->andReturn( true );
		Functions\expect( 'current_user_can' )->once()->with( 'edit_posts' )->andReturn( true );
		Functions\expect( 'restore_current_blog' )->twice();

		$api = new MslsRestApi();
		$this->assertTrue( $api->check_permission( $request ) );
	}

	public function test_check_permission_no_read_access(): void {
		$request = \Mockery::mock( \WP_REST_Request::class );
		$request->shouldReceive( 'get_param' )->with( 'source_blog_id' )->andReturn( 1 );
		$request->shouldReceive( 'get_param' )->with( 'source_post_id' )->andReturn( 10 );
		$request->shouldReceive( 'get_param' )->with( 'target_blog_id' )->andReturn( 2 );

		Functions\expect( 'switch_to_blog' )->once()->with( 1 );
		Functions\expect( 'current_user_can' )->once()->with( 'read_post', 10 )->andReturn( false );
		Functions\expect( 'restore_current_blog' )->once();

		$api = new MslsRestApi();
		$this->assertFalse( $api->check_permission( $request ) );
	}

	public function test_check_permission_no_edit_access(): void {
		$request = \Mockery::mock( \WP_REST_Request::class );
		$request->shouldReceive( 'get_param' )->with( 'source_blog_id' )->andReturn( 1 );
		$request->shouldReceive( 'get_param' )->with( 'source_post_id' )->andReturn( 10 );
		$request->shouldReceive( 'get_param' )->with( 'target_blog_id' )->andReturn( 2 );

		Functions\expect( 'switch_to_blog' )->twice();
		Functions\expect( 'current_user_can' )->once()->with( 'read_post', 10 )->andReturn( true );
		Functions\expect( 'current_user_can' )->once()->with( 'edit_posts' )->andReturn( false );
		Functions\expect( 'restore_current_blog' )->twice();

		$api = new MslsRestApi();
		$this->assertFalse( $api->check_permission( $request ) );
	}

	public function test_create_translation_source_not_found(): void {
		$request = \Mockery::mock( \WP_REST_Request::class );
		$request->shouldReceive( 'get_param' )->with( 'source_post_id' )->andReturn( 999 );
		$request->shouldReceive( 'get_param' )->with( 'source_blog_id' )->andReturn( 1 );
		$request->shouldReceive( 'get_param' )->with( 'target_blog_id' )->andReturn( 2 );

		Functions\expect( 'switch_to_blog' )->once()->with( 1 );
		Functions\expect( 'get_post' )->once()->with( 999 )->andReturn( null );
		Functions\expect( 'restore_current_blog' )->once();

		$api    = new MslsRestApi();
		$result = $api->create_translation( $request );

		$this->assertInstanceOf( \WP_Error::class, $result );
	}

	public function test_create_translation_success(): void {
		$source_post               = \Mockery::mock( \WP_Post::class );
		$source_post->ID           = 10;
		$source_post->post_title   = 'Hello World';
		$source_post->post_content = 'Some content';
		$source_post->post_type    = 'post';

		$request = \Mockery::mock( \WP_REST_Request::class );
		$request->shouldReceive( 'get_param' )->with( 'source_post_id' )->andReturn( 10 );
		$request->shouldReceive( 'get_param' )->with( 'source_blog_id' )->andReturn( 1 );
		$request->shouldReceive( 'get_param' )->with( 'target_blog_id' )->andReturn( 2 );

		Functions\expect( 'switch_to_blog' )->times( 5 );
		Functions\expect( 'restore_current_blog' )->times( 5 );
		Functions\expect( 'get_post' )->once()->with( 10 )->andReturn( $source_post );

		Functions\expect( 'get_object_taxonomies' )->once()->with( 'post' )->andReturn( array() );
		Functions\expect( 'post_type_exists' )->once()->with( 'post' )->andReturn( true );
		Functions\expect( 'wp_insert_post' )->once()->andReturn( 42 );
		Functions\expect( 'get_edit_post_link' )->once()->with( 42, 'raw' )->andReturn( 'https://example.tld/wp-admin/post.php?post=42&action=edit' );

		Functions\expect( 'get_option' )->andReturn( array() );
		Functions\expect( 'add_option' )->andReturn( true );
		Functions\expect( 'delete_option' )->andReturn( true );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_blog_id' )->andReturn( 1, 2 );

		Functions\expect( 'msls_blog_collection' )->andReturn( $collection );
		Functions\expect( 'get_blog_option' )->andReturn( 'en_US' );

		Functions\expect( 'apply_filters' )->andReturnUsing(
			function ( $hook, $value ) {
				return $value;
			}
		);

		Functions\expect( 'do_action' )->andReturnNull();

		$api    = new MslsRestApi();
		$result = $api->create_translation( $request );

		$this->assertInstanceOf( \WP_REST_Response::class, $result );
		$this->assertEquals( 201, $result->get_status() );

		$data = $result->get_data();
		$this->assertEquals( 42, $data['post_id'] );
		$this->assertEquals( 'https://example.tld/wp-admin/post.php?post=42&action=edit', $data['edit_url'] );
	}

	public function test_prefix_source_language(): void {
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'de_DE' );

		$source_post               = \Mockery::mock( \WP_Post::class );
		$source_post->post_title   = 'Hallo Welt';
		$source_post->post_content = 'Inhalt';

		$post_data = array(
			'post_title'   => 'Hallo Welt',
			'post_content' => 'Inhalt',
		);

		$result = MslsRestApi::prefix_source_language( $post_data, $source_post, 1, 2 );

		$this->assertEquals( 'From de: Hallo Welt', $result['post_title'] );
		$this->assertEquals( 'Inhalt', $result['post_content'] );
	}

	public function test_prefix_source_language_is_removable(): void {
		$this->assertTrue(
			method_exists( MslsRestApi::class, 'prefix_source_language' ),
			'prefix_source_language must be a public static method for use with remove_filter'
		);

		$reflection = new \ReflectionMethod( MslsRestApi::class, 'prefix_source_language' );
		$this->assertTrue( $reflection->isPublic() );
		$this->assertTrue( $reflection->isStatic() );
	}
}
