<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsContentFilter;
use lloc\Msls\MslsOptions;

use lloc\Msls\MslsPlugin;

class TestMslsContentFilter extends MslsUnitTestCase {

	protected function provide_content_filter_data(): array {
		return array(
			array( 'Test', 'Test', true, false, false ),
			array( 'Test', 'Test', false, false, false ),
			array( 'Test', 'Test', false, true, false ),
			array( 'Test', 'Test', false, false, true ),
			array( 'Test', 'Test', true, true, true ),
		);
	}

	/**
	 * @dataProvider provide_content_filter_data
	 */
	public function test_content_filter_empty( string $content, string $expected, bool $is_front_page, bool $is_singular, bool $is_content_filter ) {
		Functions\when( 'is_front_page' )->justReturn( $is_front_page );
		Functions\when( 'is_singular' )->justReturn( $is_singular );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( $is_content_filter );

		$test = new MslsContentFilter( $options );

		$this->assertEquals( $expected, $test->content_filter( $content ) );
	}

	public function test_content_filter_one_link() {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->once()->andReturn( 'Deutsch' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( array( $blog ) );
		$collection->shouldReceive( 'is_current_blog' )->once()->andReturn( true );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_content_filter' )->andReturn( true );
		$options->shouldReceive( 'get_flag_url' )->once()->andReturn( 'https://lloc.de/wp-content/plugins/msls/flags/de.png' );

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
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'is_singular' )->once()->andReturn( true );
		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://msls.co/testpage/' );

		$test = new MslsContentFilter( $options );

		$content  = '<p>Test>/p>';
		$expected = '<p>Test>/p><p id="msls">This post is also available in <a href="https://msls.co/testpage/" title="Deutsch" class="current_language">Deutsch</a>.</p>';
		$this->assertEquals( $expected, $test->content_filter( $content ) );
	}
}
