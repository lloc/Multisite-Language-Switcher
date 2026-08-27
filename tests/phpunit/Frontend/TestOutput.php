<?php declare( strict_types=1 );

namespace lloc\MslsTests\Frontend;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Frontend\Output;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\Msls\Options\Post\Post;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestOutput extends MslsUnitTestCase {

	private function OutputFactory(): Output {
		$options = \Mockery::mock( Options::class );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'has_current_blog' )->andReturn( true );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( 1 );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array() );

		return new Output( $options, $collection );
	}

	public function test_get_method(): void {
		$test = $this->OutputFactory();

		$this->assertEquals( array(), $test->get( 0 ) );
	}

	/**
	 * The conditional context Output::get_alternate_links() walks before it looks at a
	 * single blog. Identical for every case below, so the cases only carry the blogs.
	 */
	private function expect_query_context( Collection $collection ): void {
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$conditionals = array(
			'is_admin',
			'is_front_page',
			'is_search',
			'is_404',
			'is_category',
			'is_tag',
			'is_tax',
			'is_date',
			'is_author',
			'is_post_type_archive',
		);

		foreach ( $conditionals as $conditional ) {
			Functions\expect( $conditional )->once()->andReturn( false );
		}

		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn( array() );
	}

	/**
	 * The second column is the filter the case expects to be applied, null where none is.
	 *
	 * @return array<string, array{array<int, array<string, ?string>>, ?string, string}>
	 */
	public static function alternate_links_provider(): array {
		$de = array(
			'alpha2'      => 'de',
			'language'    => 'de_DE',
			'url'         => 'https://example.de/',
			'description' => 'Deutsch',
		);

		$it = array(
			'alpha2'      => 'it',
			'language'    => 'it_IT',
			'url'         => 'https://example.it/',
			'description' => 'Italiano',
		);

		return array(
			'two blogs with an url'           => array(
				array( $de, $it ),
				'msls_output_get_alternate_links_arr',
				'<link rel="alternate" href="https://example.de/" hreflang="de" />' . PHP_EOL .
				'<link rel="alternate" href="https://example.it/" hreflang="it" />',
			),
			'a single blog becomes x-default' => array(
				array( $de ),
				'msls_output_get_alternate_links_default',
				'<link rel="alternate" href="https://example.de/" hreflang="x-default" />',
			),
			'blog without an url'             => array(
				array(
					array(
						'alpha2'   => 'de',
						'language' => 'de_DE',
						'url'      => null,
					),
				),
				null,
				'',
			),
			'blog with an empty url'          => array(
				array(
					array(
						'alpha2'   => 'de',
						'language' => 'de_DE',
						'url'      => '',
					),
				),
				null,
				'',
			),
		);
	}

	/**
	 * @param array<int, array<string, ?string>> $blogs
	 */
	#[DataProvider( 'alternate_links_provider' )]
	public function test_get_alternate_links( array $blogs, ?string $filter, string $expected ): void {
		$objects = array();

		foreach ( $blogs as $blog ) {
			$mock = \Mockery::mock( Blog::class );
			$mock->shouldReceive( 'get_alpha2' )->andReturn( $blog['alpha2'] );
			$mock->shouldReceive( 'get_language' )->andReturn( $blog['language'] );
			$mock->shouldReceive( 'get_url' )->andReturn( $blog['url'] );

			if ( isset( $blog['description'] ) ) {
				$mock->shouldReceive( 'get_description' )->andReturn( $blog['description'] );
			}

			$objects[] = $mock;
		}

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $objects );

		$this->expect_query_context( $collection );

		if ( null !== $filter ) {
			Filters\expectApplied( $filter )->once();
		}

		$test = $this->OutputFactory();

		$this->assertEquals( $expected, $test->get_alternate_links() );
	}

	public function test___toString_no_translation(): void {
		$expected = '<a href="https://example.com" title="Example">Example</a>';

		Filters\expectApplied( 'msls_output_no_translation_found' )->once()->andReturn( $expected );

		$test = $this->OutputFactory();

		$this->assertEquals( $expected, strval( $test ) );
	}

	public function test___toString_output(): void {
		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->andReturn( false );

		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'is_tax' )->once()->andReturn( false );
		Functions\expect( 'is_date' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'home_url' )->once()->andReturnFirstArg();

		$expected = '<a href="/" title="Deutsch"><img src="https://msls.co/wp-content/plugins/msls/flags/de.png" alt="de_DE"/> Deutsch</a>';

		$test = new Output( $options, $collection );

		$this->assertEquals( $expected, strval( $test ) );
	}

	public function test___toString_current_blog(): void {
		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->andReturn( true );

		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'is_tax' )->once()->andReturn( false );
		Functions\expect( 'is_date' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/de/testpage/' );

		$expected = '<a href="https://msls.co/de/testpage/" title="Deutsch" class="current_language" aria-current="page"><img src="https://msls.co/wp-content/plugins/msls/flags/de.png" alt="de_DE"/> Deutsch</a>';

		$this->assertEquals( $expected, strval( new Output( $options, $collection ) ) );
	}

	public function test___toString_filter(): void {
		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->andReturn( true );

		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'is_tax' )->once()->andReturn( false );
		Functions\expect( 'is_date' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/de/testpage/' );
		Functions\expect( 'has_filter' )->once()->with( 'msls_output_get' )->andReturn( true );

		$expected = '<a href="https://msls.co/de/testpage/" title="Deutsch"> <img src="https://msls.co/wp-content/plugins/msls/flags/de.png" alt="de_DE"/>Deutsch</a>';
		Filters\expectApplied( 'msls_output_get' )->once()->andReturn( $expected );

		$this->assertEquals( $expected, strval( new Output( $options, $collection ) ) );
	}

	public function test_get_not_fulfilled(): void {
		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->andReturn( false );

		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'is_tax' )->once()->andReturn( false );
		Functions\expect( 'is_date' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$this->assertEquals( array(), ( new Output( $options, $collection ) )->get( 0, false, true ) );
	}

	public function test_get_tags(): void {
		$test = $this->OutputFactory();

		$this->assertIsArray( $test->get_tags() );
	}

	public function test_set_tags(): void {
		Functions\expect( 'wp_parse_args' )->once()->andReturn( array() );

		$test = $this->OutputFactory();

		$this->assertInstanceOf( Output::class, $test->set_tags() );
	}

	/**
	 * @return array<string, array{?class-string, bool, bool}>
	 */
	public static function requirements_provider(): array {
		return array(
			'no data, translations optional'    => array( null, false, false ),
			'no data, translations required'    => array( null, true, true ),
			'options, translations optional'    => array( Options::class, false, false ),
			'options, translations required'    => array( Options::class, true, false ),
			'post options, optional'            => array( Post::class, false, false ),
			'post options without translations' => array( Post::class, true, true ),
		);
	}

	/**
	 * @param ?class-string $data_class
	 */
	#[DataProvider( 'requirements_provider' )]
	public function test_is_requirements_not_fulfilled( ?string $data_class, bool $only_with_translation, bool $expected ): void {
		$mydata = null;

		if ( null !== $data_class ) {
			Functions\expect( 'get_option' )->once()->andReturn( array() );

			$mydata = new $data_class();
		}

		$test = $this->OutputFactory();

		$this->assertSame( $expected, $test->is_requirements_not_fulfilled( $mydata, $only_with_translation, 'de_DE' ) );
	}

	public function test_get_skips_empty_url(): void {
		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );
		$blog->userblog_id = 2;

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->andReturn( false );

		Functions\expect( 'is_admin' )->atLeast()->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->atLeast()->once()->andReturn( false );
		Functions\expect( 'is_search' )->andReturn( false );
		Functions\expect( 'is_404' )->andReturn( false );
		Functions\expect( 'is_category' )->atLeast()->once()->andReturn( true );
		Functions\expect( 'is_tag' )->andReturn( false );
		Functions\expect( 'is_tax' )->andReturn( false );
		Functions\expect( 'is_woocommerce' )->andReturn( false );
		Functions\expect( 'get_queried_object_id' )->atLeast()->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->atLeast()->once()->andReturn( array() );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$this->assertEquals( array(), ( new Output( $options, $collection ) )->get( 0 ) );
	}

	public function test_init(): void {
		Functions\expect( '_deprecated_function' )->once();

		$options    = \Mockery::mock( Options::class );
		$collection = \Mockery::mock( Collection::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$this->assertInstanceOf( Output::class, Output::init() );
	}
}
