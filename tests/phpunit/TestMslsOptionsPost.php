<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsPost;

final class TestMslsOptionsPost extends MslsUnitTestCase {

	private function MslsOptionsPostFactory(): MslsOptionsPost {
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		return new MslsOptionsPost( 42 );
	}

	public function test_get_postlink_not_has_value(): void {
		$test = $this->MslsOptionsPostFactory();

		$this->assertEquals( '', $test->get_postlink( 'es_ES' ) );
	}

	public function test_get_postlink_post_is_null(): void {
		Functions\expect( 'get_post' )->once()->andReturnNull();

		$test = $this->MslsOptionsPostFactory();

		$this->assertEquals( '', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_post_is_draft(): void {
		$post              = \Mockery::mock( '\WP_Post' );
		$post->post_status = 'draft';

		Functions\expect( 'get_post' )->once()->andReturn( $post );

		$test = $this->MslsOptionsPostFactory();

		$this->assertEquals( '', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_post_is_published(): void {
		$post              = \Mockery::mock( '\WP_Post' );
		$post->post_status = 'publish';
		$post->post_type   = 'post';

		Functions\expect( 'get_post' )->once()->andReturn( $post );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( (object) array( 'rewrite' => array( 'with_front' => true ) ) );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://example.de/a-post' );

		$test = $this->MslsOptionsPostFactory();

		Filters\expectApplied( 'check_url' )->once()->with( 'https://example.de/a-post', $test );

		$this->assertEquals( 'https://example.de/a-post', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/a-post' );

		$test = $this->MslsOptionsPostFactory();

		$this->assertEquals( 'https://msls.co/a-post', $test->get_current_link() );
	}

	public function test_get_option_name(): void {
		$test = $this->MslsOptionsPostFactory();

		$this->assertSame( 'msls_42', $test->get_option_name() );
	}
}
