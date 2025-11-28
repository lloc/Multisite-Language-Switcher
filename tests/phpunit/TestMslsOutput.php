<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOptionsPost;
use lloc\Msls\MslsOutput;

final class TestMslsOutput extends MslsUnitTestCase {

	private function MslsOutputFactory(): MslsOutput {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'has_current_blog' )->andReturn( true );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( 1 );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array() );

		return new MslsOutput( $options, $collection );
	}

	public function test_get_method(): void {
		$test = $this->MslsOutputFactory();

		$this->assertEquals( array(), $test->get( 0 ) );
	}

	public function test_get_alternate_links_two_url(): void {
		$blogs = array();

		$a = \Mockery::mock( MslsBlog::class );
		$a->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$a->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$a->shouldReceive( 'get_url' )->andReturn( 'https://example.de/' );
		$a->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$blogs[] = $a;

		$b = \Mockery::mock( MslsBlog::class );
		$b->shouldReceive( 'get_alpha2' )->andReturn( 'it' );
		$b->shouldReceive( 'get_language' )->andReturn( 'it_IT' );
		$b->shouldReceive( 'get_url' )->andReturn( 'https://example.it/' );
		$b->shouldReceive( 'get_description' )->andReturn( 'Italiano' );

		$blogs[] = $b;

		$collection = \Mockery::mock( MslsBlogCollection::class );
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

		$test = $this->MslsOutputFactory();

		$this->assertEquals( $expected, $test->get_alternate_links() );
	}

	public function test_get_alternate_links_null_url(): void {
		$blogs = array();

		$a = \Mockery::mock( MslsBlog::class );
		$a->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$a->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$a->shouldReceive( 'get_url' )->andReturnNull();

		$blogs[] = $a;

		$collection = \Mockery::mock( MslsBlogCollection::class );
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

		$test = $this->MslsOutputFactory();

		$this->assertEquals( '', $test->get_alternate_links() );
	}

	public function test_get_alternate_links_one_url(): void {
		$blogs = array();

		$a = \Mockery::mock( MslsBlog::class );
		$a->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$a->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$a->shouldReceive( 'get_url' )->andReturn( 'https://example.de/' );
		$a->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$blogs[] = $a;

		$collection = \Mockery::mock( MslsBlogCollection::class );
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

		$test = $this->MslsOutputFactory();

		$this->assertEquals( $expected, $test->get_alternate_links() );
	}

	public function test___toString_no_translation(): void {
		$expected = '<a href="https://example.com" title="Example">Example</a>';

		Filters\expectApplied( 'msls_output_no_translation_found' )->once()->andReturn( $expected );

		$test = $this->MslsOutputFactory();

		$this->assertEquals( $expected, strval( $test ) );
	}

	public function test___toString_output(): void {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
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

		$test = new MslsOutput( $options, $collection );

		$this->assertEquals( $expected, strval( $test ) );
	}

	public function test___toString_current_blog(): void {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
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

		$this->assertEquals( $expected, strval( new MslsOutput( $options, $collection ) ) );
	}

	public function test___toString_filter(): void {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
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

		$this->assertEquals( $expected, strval( new MslsOutput( $options, $collection ) ) );
	}

	public function test_get_not_fulfilled(): void {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://msls.co/wp-content/plugins/msls/flags/de.png' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
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

		$this->assertEquals( array(), ( new MslsOutput( $options, $collection ) )->get( 0, false, true ) );
	}

	public function test_get_tags(): void {
		$test = $this->MslsOutputFactory();

		$this->assertIsArray( $test->get_tags() );
	}

	public function test_set_tags(): void {
		Functions\expect( 'wp_parse_args' )->once()->andReturn( array() );

		$test = $this->MslsOutputFactory();

		$this->assertInstanceOf( MslsOutput::class, $test->set_tags() );
	}

	public function test_is_requirements_not_fulfilled_with_null(): void {
		$test = $this->MslsOutputFactory();

		$this->assertFalse( $test->is_requirements_not_fulfilled( null, false, 'de_DE' ) );
		$this->assertTrue( $test->is_requirements_not_fulfilled( null, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptions(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new MslsOptions();

		$test = $this->MslsOutputFactory();

		$this->assertFalse( $test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertFalse( $test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptionspost(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new MslsOptionsPost();

		$test = $this->MslsOutputFactory();

		$this->assertFalse( $test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertTrue( $test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}

	public function test_init(): void {
		Functions\expect( '_deprecated_function' )->once();

		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$this->assertInstanceOf( MslsOutput::class, MslsOutput::init() );
	}
}
