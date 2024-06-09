<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsContentFilter;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOptionsPost;
use lloc\Msls\MslsOutput;

class TestMslsOutput extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'has_current_blog' )->andReturn( true );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( 1 );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array() );

		$this->test = new MslsOutput( $options, $collection );
	}

	public function test_get_method(): void {
		$this->assertEquals( array(), $this->test->get( 0 ) );
	}

	public function test_get_alternate_links_two_url() {
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

		Filters\expectApplied( 'mlsl_output_get_alternate_links_arr' )->once();

		$expected =
			'<link rel="alternate" hreflang="de" href="https://example.de/" title="Deutsch" />' . PHP_EOL .
			'<link rel="alternate" hreflang="it" href="https://example.it/" title="Italiano" />';
		$this->assertEquals( $expected, $this->test->get_alternate_links() );
	}

	public function test_get_alternate_links_null_url() {
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

		$this->assertEquals( '', $this->test->get_alternate_links() );
	}

	public function test_get_alternate_links_one_url() {
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

		Filters\expectApplied( 'mlsl_output_get_alternate_links_default' )->once();

		$this->assertEquals( '<link rel="alternate" hreflang="x-default" href="https://example.de/" title="Deutsch" />', $this->test->get_alternate_links() );
	}

	public function test___toString_no_translation() {
		$expected = '<a href="https://example.com" title="Example">Example</a>';

		Filters\expectApplied( 'msls_output_no_translation_found' )->once()->andReturn( $expected );
		$this->assertEquals( $expected, strval( $this->test ) );
	}

	public function test___toString_output() {
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

		$test = new MslsOutput( $options, $collection );

		$this->assertEquals( '<a href="/" title="Deutsch"><img src="https://msls.co/wp-content/plugins/msls/flags/de.png" alt="de_DE"/> Deutsch</a>', strval( $test ) );
	}

	public function test___toString_current_blog() {
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

		$expected = '<a href="https://msls.co/de/testpage/" title="Deutsch" class="current_language"><img src="https://msls.co/wp-content/plugins/msls/flags/de.png" alt="de_DE"/> Deutsch</a>';

		$this->assertEquals( $expected, strval( new MslsOutput( $options, $collection ) ) );
	}

	public function test___toString_filter() {
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

	public function test_get_not_fulfilled() {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );
		// $blog->shouldReceive( 'get_description' )->once()->andReturn( 'Deutsch' );

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
		$this->assertIsArray( $this->test->get_tags() );
	}

	public function test_set_tags(): void {
		Functions\expect( 'wp_parse_args' )->once()->andReturn( array() );

		$this->assertInstanceOf( MslsOutput::class, $this->test->set_tags() );
	}

	public function test_is_requirements_not_fulfilled_with_null(): void {
		$this->assertFalse( $this->test->is_requirements_not_fulfilled( null, false, 'de_DE' ) );
		$this->assertTrue( $this->test->is_requirements_not_fulfilled( null, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptions(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new MslsOptions();

		$this->assertFalse( $this->test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertFalse( $this->test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptionspost(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new MslsOptionsPost();

		$this->assertFalse( $this->test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertTrue( $this->test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}
}
