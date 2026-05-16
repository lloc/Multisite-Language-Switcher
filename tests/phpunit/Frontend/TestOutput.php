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

	public function test_get_alternate_links_two_url(): void {
		$blogs = array();

		$a = \Mockery::mock( Blog::class );
		$a->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$a->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$a->shouldReceive( 'get_url' )->andReturn( 'https://example.de/' );
		$a->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$blogs[] = $a;

		$b = \Mockery::mock( Blog::class );
		$b->shouldReceive( 'get_alpha2' )->andReturn( 'it' );
		$b->shouldReceive( 'get_language' )->andReturn( 'it_IT' );
		$b->shouldReceive( 'get_url' )->andReturn( 'https://example.it/' );
		$b->shouldReceive( 'get_description' )->andReturn( 'Italiano' );

		$blogs[] = $b;

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
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

		Filters\expectApplied( 'msls_output_get_alternate_links_arr' )->once();

		$expected =
			'<link rel="alternate" href="https://example.de/" hreflang="de" />' . PHP_EOL .
			'<link rel="alternate" href="https://example.it/" hreflang="it" />';

		$test = $this->OutputFactory();

		$this->assertEquals( $expected, $test->get_alternate_links() );
	}

	public function test_get_alternate_links_null_url(): void {
		$blogs = array();

		$a = \Mockery::mock( Blog::class );
		$a->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$a->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$a->shouldReceive( 'get_url' )->andReturnNull();

		$blogs[] = $a;

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
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

		$test = $this->OutputFactory();

		$this->assertEquals( '', $test->get_alternate_links() );
	}

	public function test_get_alternate_links_one_url(): void {
		$blogs = array();

		$a = \Mockery::mock( Blog::class );
		$a->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$a->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$a->shouldReceive( 'get_url' )->andReturn( 'https://example.de/' );
		$a->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$blogs[] = $a;

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
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

		Filters\expectApplied( 'msls_output_get_alternate_links_default' )->once();

		$expected = '<link rel="alternate" href="https://example.de/" hreflang="x-default" />';

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

	public function test_is_requirements_not_fulfilled_with_null(): void {
		$test = $this->OutputFactory();

		$this->assertFalse( $test->is_requirements_not_fulfilled( null, false, 'de_DE' ) );
		$this->assertTrue( $test->is_requirements_not_fulfilled( null, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptions(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new Options();

		$test = $this->OutputFactory();

		$this->assertFalse( $test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertFalse( $test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptionspost(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new Post();

		$test = $this->OutputFactory();

		$this->assertFalse( $test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertTrue( $test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}

	public function test_get_alternate_links_empty_url(): void {
		$blogs = array();

		$a = \Mockery::mock( Blog::class );
		$a->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$a->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$a->shouldReceive( 'get_url' )->andReturn( '' );

		$blogs[] = $a;

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
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

		$test = $this->OutputFactory();

		$this->assertEquals( '', $test->get_alternate_links() );
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
