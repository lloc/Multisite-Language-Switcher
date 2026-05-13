<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options;

use lloc\MslsTests\MslsUnitTestCase;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Options\OptionsPost;

final class TestOptionsPost extends MslsUnitTestCase {

	private function OptionsPostFactory(): OptionsPost {
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		return new OptionsPost( 42 );
	}

	public function test_get_postlink_not_has_value(): void {
		$test = $this->OptionsPostFactory();

		$this->assertEquals( '', $test->get_postlink( 'es_ES' ) );
	}

	public function test_get_postlink_post_is_null(): void {
		Functions\expect( 'get_post' )->once()->andReturnNull();

		$test = $this->OptionsPostFactory();

		$this->assertEquals( '', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_post_is_draft(): void {
		$post              = \Mockery::mock( '\WP_Post' );
		$post->post_status = 'draft';

		Functions\expect( 'get_post' )->once()->andReturn( $post );

		$test = $this->OptionsPostFactory();

		$this->assertEquals( '', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_post_is_published(): void {
		$post              = \Mockery::mock( '\WP_Post' );
		$post->post_status = 'publish';
		$post->post_type   = 'post';

		Functions\expect( 'get_post' )->once()->andReturn( $post );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( (object) array( 'rewrite' => array( 'with_front' => true ) ) );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://example.de/a-post' );

		$test = $this->OptionsPostFactory();

		Filters\expectApplied( 'check_url' )->once()->with( 'https://example.de/a-post', $test );

		$this->assertEquals( 'https://example.de/a-post', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/a-post' );

		$test = $this->OptionsPostFactory();

		$this->assertEquals( 'https://msls.co/a-post', $test->get_current_link() );
	}

	public function test_get_option_name(): void {
		$test = $this->OptionsPostFactory();

		$this->assertSame( 'msls_42', $test->get_option_name() );
	}
}
