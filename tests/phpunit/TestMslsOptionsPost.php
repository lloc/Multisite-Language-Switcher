<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsPost;

class TestMslsOptionsPost extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		$this->test = new MslsOptionsPost( 42 );
	}

	public function test_get_postlink_not_has_value() {
		$this->assertEquals( '', $this->test->get_postlink( 'es_ES' ) );
	}

	public function test_get_postlink_post_is_null(): void {
		Functions\expect( 'get_post' )->once()->andReturnNull();

		$this->assertEquals( '', $this->test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_post_is_draft(): void {
		$post              = \Mockery::mock( '\WP_Post' );
		$post->post_status = 'draft';

		Functions\expect( 'get_post' )->once()->andReturn( $post );

		$this->assertEquals( '', $this->test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_post_is_published(): void {
		$post              = \Mockery::mock( '\WP_Post' );
		$post->post_status = 'publish';
		$post->post_type   = 'post';

		Functions\expect( 'get_post' )->once()->andReturn( $post );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( (object) array( 'rewrite' => array( 'with_front' => true ) ) );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://example.de/a-post' );

		Filters\expectApplied( 'check_url' )->once()->with( 'https://example.de/a-post', $this->test );

		$this->assertEquals( 'https://example.de/a-post', $this->test->get_postlink( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/a-post' );

		$this->assertEquals( 'https://msls.co/a-post', $this->test->get_current_link() );
	}

	public function test_get_option_name() {
		$this->assertSame( 'msls_42', $this->test->get_option_name() );
	}
}
