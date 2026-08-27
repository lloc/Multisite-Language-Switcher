<?php declare( strict_types=1 );

namespace lloc\MslsTests\Frontend;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Frontend\ContentFilter;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestContentFilter extends MslsUnitTestCase {

	public function test_init(): void {
		$options = \Mockery::mock( Options::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );

		Filters\expectAdded( 'the_content' )->once();

		$this->expectNotToPerformAssertions();
		ContentFilter::init();
	}

	/**
	 * The truth table of the three conditions that keep content_filter() from adding a hint.
	 *
	 * @return array<string, array{bool, bool, bool}>
	 */
	public static function content_filter_provider(): array {
		return array(
			'front page, not singular, filter off' => array( true, false, false ),
			'nothing enabled'                      => array( false, false, false ),
			'singular, filter off'                 => array( false, true, false ),
			'filter on, not singular'              => array( false, false, true ),
			'front page, singular, filter on'      => array( true, true, true ),
		);
	}

	#[DataProvider( 'content_filter_provider' )]
	public function test_content_filter_empty( bool $is_front_page, bool $is_singular, bool $is_content_filter ) {
		Functions\when( 'is_front_page' )->justReturn( $is_front_page );
		Functions\when( 'is_singular' )->justReturn( $is_singular );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( $is_content_filter );

		$test = new ContentFilter( $options );

		$this->assertEquals( 'Test', $test->content_filter( 'Test' ) );
	}

	public function test_content_filter_one_link(): void {
		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->once()->andReturn( 'Deutsch' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->once()->andReturn( false );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( true );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$post              = \Mockery::mock( 'WP_Post' );
		$post->post_status = 'publish';
		$post->post_type   = 'post';

		$post_object                        = \Mockery::mock( 'WP_Post_Type' );
		$post_object->rewrite['with_front'] = true;

		Functions\expect( 'get_post' )->once()->andReturn( $post );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( $post_object );
		Functions\expect( 'is_front_page' )->twice()->andReturn( false );
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'is_tax' )->once()->andReturn( false );
		Functions\expect( 'is_date' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/testpage/' );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$test = new ContentFilter( $options );

		$content  = '<p>Test>/p>';
		$expected = '<p>Test>/p><p id="msls">This post is also available in <a href="https://msls.co/testpage/" title="Deutsch">Deutsch</a>.</p>';
		$this->assertEquals( $expected, $test->content_filter( $content ) );
	}

	public function test_content_filter_zero_links(): void {
		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( array() );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( true );

		$post              = \Mockery::mock( 'WP_Post' );
		$post->post_status = 'publish';
		$post->post_type   = 'post';

		$post_object                        = \Mockery::mock( 'WP_Post_Type' );
		$post_object->rewrite['with_front'] = true;

		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$test = new ContentFilter( $options );

		$content  = '<p>Test>/p>';
		$expected = '<p>Test>/p>';
		$this->assertEquals( $expected, $test->content_filter( $content ) );
	}

	public function test_content_filter_more_links(): void {
		$a = \Mockery::mock( Blog::class );
		$a->shouldReceive( 'get_language' )->once()->andReturn( 'it_IT' );
		$a->shouldReceive( 'get_description' )->once()->andReturn( 'Italiano' );

		$b = \Mockery::mock( Blog::class );
		$b->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );
		$b->shouldReceive( 'get_description' )->once()->andReturn( 'Deutsch' );

		$c = \Mockery::mock( Blog::class );
		$c->shouldReceive( 'get_language' )->once()->andReturn( 'fr_FR' );
		$c->shouldReceive( 'get_description' )->once()->andReturn( 'Français' );

		$blogs = array( $a, $b, $c );
		$times = count( $blogs );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( $blogs );
		$collection->shouldReceive( 'is_current_blog' )->times( $times )->andReturn( false );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( true );
		$options->shouldReceive( 'get_flag_url' )->times( $times )->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$post              = \Mockery::mock( 'WP_Post' );
		$post->post_status = 'publish';
		$post->post_type   = 'post';

		$post_object                        = \Mockery::mock( 'WP_Post_Type' );
		$post_object->rewrite['with_front'] = true;

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		Functions\expect( 'get_post_type_object' )->once()->andReturn( $post_object );
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'is_front_page' )->twice()->andReturn( false );
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'is_tax' )->once()->andReturn( false );
		Functions\expect( 'is_date' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn(
			array(
				'de_DE' => 42,
				'it_IT' => 17,
				'fr_FR' => 23,
			)
		);
		Functions\expect( 'get_post' )->times( $times )->andReturn( $post );
		Functions\expect( 'get_permalink' )->times( $times )->andReturn( 'https://msls.co/de/testpage/' );
		Functions\expect( 'switch_to_blog' )->times( $times );
		Functions\expect( 'restore_current_blog' )->times( $times );

		$test = new ContentFilter( $options );

		$content  = '<p>Test>/p>';
		$expected = '<p>Test>/p><p id="msls">This post is also available in <a href="https://msls.co/de/testpage/" title="Italiano">Italiano</a>, <a href="https://msls.co/de/testpage/" title="Deutsch">Deutsch</a> and <a href="https://msls.co/de/testpage/" title="Français">Français</a>.</p>';
		$this->assertEquals( $expected, $test->content_filter( $content ) );
	}

	public function test_content_filter_with_filter(): void {
		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->once()->andReturn( 'Deutsch' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->once()->andReturn( false );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( true );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$post              = \Mockery::mock( 'WP_Post' );
		$post->post_status = 'publish';
		$post->post_type   = 'post';

		$post_object                        = \Mockery::mock( 'WP_Post_Type' );
		$post_object->rewrite['with_front'] = true;

		Functions\expect( 'get_post' )->once()->andReturn( $post );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( $post_object );
		Functions\expect( 'is_front_page' )->twice()->andReturn( false );
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'is_tax' )->once()->andReturn( false );
		Functions\expect( 'is_date' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/testpage/' );
		Functions\expect( 'has_filter' )->once()->with( 'msls_filter_string' )->andReturn( true );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$test = new ContentFilter( $options );

		$content = '<p>Test>/p>';
		$filter  = 'This post is also available in <a href="https://msls.co/testpage/" title="Deutsch">Deutsch</a>.';

		Filters\expectApplied( 'msls_filter_string' )->once()->andReturn( $filter );

		$expected = '<p>Test>/p><p id="msls">' . $filter . '</p>';
		$this->assertEquals( $expected, $test->content_filter( $content ) );
	}
}
