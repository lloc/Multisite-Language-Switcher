<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Post;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Options\Post\Post;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Filters;
use function Brain\Monkey\Functions;

final class TestPost extends MslsUnitTestCase {

	private function OptionsPostFactory(): Post {
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		return new Post( 42 );
	}

	public function test_get_max_pages_counts_the_nextpage_quicktags(): void {
		$post               = \Mockery::mock( '\WP_Post' );
		$post->post_content = 'One<!--nextpage-->Two<!--nextpage-->Three';

		Functions\expect( 'get_post' )->once()->with( 42 )->andReturn( $post );

		$test = $this->OptionsPostFactory();

		$this->assertEquals( 3, $test->get_max_pages( 'de_DE', Post::PAGINATION_SINGLE ) );
	}

	public function test_get_max_pages_falls_back_to_the_source_post(): void {
		$post               = \Mockery::mock( '\WP_Post' );
		$post->post_content = 'Not split at all';

		Functions\expect( 'get_post' )->once()->with( 42 )->andReturn( $post );

		$test = $this->OptionsPostFactory();

		$this->assertEquals( 1, $test->get_max_pages( 'es_ES', Post::PAGINATION_SINGLE ) );
	}

	public function test_get_max_pages_without_a_post(): void {
		Functions\expect( 'get_post' )->once()->andReturnNull();

		$test = $this->OptionsPostFactory();

		$this->assertEquals( 0, $test->get_max_pages( 'de_DE', Post::PAGINATION_SINGLE ) );
	}

	public function test_get_max_pages_of_the_posts_page(): void {
		Functions\when( 'is_home' )->justReturn( true );
		Functions\when( 'wp_count_posts' )->justReturn( (object) array( 'publish' => 25 ) );

		$test = $this->OptionsPostFactory();

		Functions\expect( 'get_option' )->once()->with( 'posts_per_page', 10 )->andReturn( 10 );

		$this->assertEquals( 3, $test->get_max_pages( 'de_DE', Post::PAGINATION_ARCHIVE ) );
	}

	public function test_get_max_pages_of_a_single_post_request(): void {
		Functions\when( 'is_home' )->justReturn( false );

		$test = $this->OptionsPostFactory();

		$this->assertEquals( 0, $test->get_max_pages( 'de_DE', Post::PAGINATION_ARCHIVE ) );
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
