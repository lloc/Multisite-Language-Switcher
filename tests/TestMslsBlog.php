<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use Brain\Monkey\Functions;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;

class TestMslsBlog extends MslsUnitTestCase {

	public function get_blog(): MslsBlog {
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'it_IT' );
		Functions\expect( 'plugin_dir_path' )->atLeast( 1 )->andReturn( dirname( __DIR__, 1 ) . '/' );

		$blog              = new \stdClass();
		$blog->userblog_id = 1;
		$blog->blogname    = 'Test';

		return new MslsBlog( $blog, 'Italiano' );
	}

	public function test_get_userblog_id(): void {
		$this->assertEquals( 1, $this->get_blog()->userblog_id );
	}

	public function test_get_description(): void {
		$this->assertEquals( 'Italiano', $this->get_blog()->get_description() );
	}

	public function test_get_url_current(): void {
		$url = 'https://example.org/';

		$option = \Mockery::mock( MslsOptions::class );
		$option->shouldReceive( 'get_current_link' )->andReturn( $url );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturn( 1 );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$this->assertEquals( $url, $this->get_blog()->get_url( $option ) );
	}

	public function test_get_frontpage(): void {
		$url = 'https://example.org/';

		$option = \Mockery::mock( MslsOptions::class );
		$option->shouldReceive( 'get_permalink' )->once()->andReturn( $url );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturn( 2 );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'is_front_page' )->once()->andReturn( true );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$this->assertEquals( $url, $this->get_blog()->get_url( $option ) );
	}

	public function test_get_url(): void {
		$url = 'https://example.org/';

		$option = \Mockery::mock( MslsOptions::class );
		$option->shouldReceive( 'get_permalink' )->once()->andReturn( $url );
		$option->shouldReceive( 'has_value' )->once()->andReturn( true );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturn( 2 );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$this->assertEquals( $url, $this->get_blog()->get_url( $option ) );
	}

	public function test_get_language(): void {
		$this->assertEquals( 'it_IT', $this->get_blog()->get_language() );
	}

	public function test_get_alpha2(): void {
		$this->assertEquals( 'it', $this->get_blog()->get_alpha2() );
	}

	public function test_get_title(): void {
		Functions\expect( 'add_query_arg' )->once()->andReturn( 'https://example.org/added-args' );

		$this->assertEquals(
			'Test <span class="msls-icon-wrapper flag"><span class="flag-icon flag-icon-it">it_IT</span></span>',
			$this->get_blog()->get_title()
		);
	}

	/**
	 * Dataprovider
	 *
	 * @return array[]
	 */
	public function compareProvider() {
		return [
			[ 0, 0, 0 ],
			[ 0, 1, - 1 ],
			[ 1, 0, 1 ],
			[ - 1, - 2, 1 ],
			[ - 2, - 1, - 1 ],
		];
	}

	/**
	 * Verify the _cmp-method
	 * @dataProvider compareProvider
	 */
	public function test__cmp_method( int $a, int $b, int $expected ): void {
		$this->assertEquals( $expected, MslsBlog::_cmp( $a, $b ) );

		$obj = new MslsBlog( null, null );
		$this->assertEquals( $expected, $obj->_cmp( $a, $b ) );
	}

	/**
	 * Verify the language-method
	 */
	public function test_language_cmp(): void {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );

		$this->assertEquals( 0, $a->language( $a, $b ) );
	}

	/**
	 * Verify the description-method
	 */
	public function test_description_cmp(): void {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );

		$this->assertEquals( 0, $a->description( $a, $b ) );
	}

	public function test_get_blavatar_lazy(): void {
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'has_site_icon' )->once()->andReturn( true );
		Functions\expect( 'get_site_icon_url' )->twice()->andReturn( 'https://example.org/icons/abc.png' );
		Functions\expect( 'wp_lazy_loading_enabled' )->once()->andReturn( true );

		$this->assertEquals(
			'<img class="blavatar" src="https://example.org/icons/abc.png" srcset="https://example.org/icons/abc.png 2x" alt="" width="16" height="16" loading="lazy" />',
			$this->get_blog()->get_blavatar()
		);
	}

	public function test_get_blavatar(): void {
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'has_site_icon' )->once()->andReturn( true );
		Functions\expect( 'get_site_icon_url' )->twice()->andReturn( 'https://example.org/icons/abc.png' );
		Functions\expect( 'wp_lazy_loading_enabled' )->once()->andReturn( false );

		$this->assertEquals(
			'<img class="blavatar" src="https://example.org/icons/abc.png" srcset="https://example.org/icons/abc.png 2x" alt="" width="16" height="16" />',
			$this->get_blog()->get_blavatar()
		);
	}

}
