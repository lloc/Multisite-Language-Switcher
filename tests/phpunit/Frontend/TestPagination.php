<?php declare( strict_types=1 );

namespace lloc\MslsTests\Frontend;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Frontend\Pagination;
use lloc\Msls\Options\Options;
use lloc\Msls\Options\OptionsInterface;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestPagination extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['wp_rewrite']                  = new \stdClass();
		$GLOBALS['wp_rewrite']->pagination_base = 'page';

		Functions\when( 'trailingslashit' )->alias(
			function ( string $string ): string {
				return rtrim( $string, '/\\' ) . '/';
			}
		);
		Functions\when( 'untrailingslashit' )->alias(
			function ( string $string ): string {
				return rtrim( $string, '/\\' );
			}
		);
		Functions\when( 'add_query_arg' )->alias(
			function ( string $key, $value, string $url ): string {
				return $url . ( str_contains( $url, '?' ) ? '&' : '?' ) . $key . '=' . $value;
			}
		);
		Functions\when( 'home_url' )->justReturn( 'https://example.com/' );
	}

	protected function tearDown(): void {
		unset( $GLOBALS['wp_rewrite'] );

		parent::tearDown();
	}

	private function optionsWithPages( int $max_pages ): OptionsInterface {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_max_pages' )->andReturn( $max_pages );

		return $options;
	}

	public function test_create_reads_the_query_vars(): void {
		Functions\expect( 'get_query_var' )->once()->with( 'paged' )->andReturn( '3' );
		Functions\expect( 'get_query_var' )->once()->with( 'page' )->andReturn( '0' );

		$test = Pagination::create();

		$this->assertTrue( $test->is_active() );
		$this->assertEquals( 3, $test->get_page() );
		$this->assertEquals( Options::PAGINATION_ARCHIVE, $test->get_context() );
	}

	/**
	 * @return array<string, array{int, int, bool}>
	 */
	public static function is_active_provider(): array {
		return array(
			'not paginated at all'  => array( 0, 0, false ),
			'first page of a list'  => array( 1, 0, false ),
			'first page of a post'  => array( 0, 1, false ),
			'third page of a list'  => array( 3, 0, true ),
			'second page of a post' => array( 0, 2, true ),
			'negative query var'    => array( -3, 0, false ),
		);
	}

	#[DataProvider( 'is_active_provider' )]
	public function test_is_active( int $paged, int $page, bool $expected ): void {
		$this->assertEquals( $expected, ( new Pagination( $paged, $page ) )->is_active() );
	}

	public function test_is_active_can_be_switched_off(): void {
		Filters\expectApplied( Pagination::MSLS_PRESERVE_HOOK )->once()->andReturn( false );

		$this->assertFalse( ( new Pagination( 3 ) )->is_active() );
	}

	public function test_get_returns_an_empty_url_untouched(): void {
		$this->assertEquals( '', ( new Pagination( 3 ) )->get( '', $this->optionsWithPages( 5 ), 'it_IT' ) );
	}

	public function test_get_leaves_an_unpaginated_request_alone(): void {
		$url = 'https://example.com/main-dishes/';

		$this->assertEquals( $url, ( new Pagination( 0 ) )->get( $url, $this->optionsWithPages( 5 ), 'it_IT' ) );
	}

	public function test_get_adds_the_page_of_an_archive(): void {
		Functions\expect( 'get_option' )->twice()->andReturn( '/%postname%/' );

		$test = new Pagination( 3 );

		$this->assertEquals(
			'https://example.com/main-dishes/page/3/',
			$test->get( 'https://example.com/main-dishes/', $this->optionsWithPages( 5 ), 'it_IT' )
		);
	}

	public function test_get_falls_back_to_the_first_page_when_the_blog_is_shorter(): void {
		$url = 'https://example.com/main-dishes/';

		$this->assertEquals( $url, ( new Pagination( 3 ) )->get( $url, $this->optionsWithPages( 2 ), 'it_IT' ) );
	}

	public function test_get_uses_the_query_string_without_pretty_permalinks(): void {
		Functions\expect( 'get_option' )->once()->andReturn( '' );

		$this->assertEquals(
			'https://example.com/?cat=7&paged=3',
			( new Pagination( 3 ) )->get( 'https://example.com/?cat=7', $this->optionsWithPages( 5 ), 'it_IT' )
		);
	}

	public function test_get_keeps_a_query_string_behind_the_page(): void {
		Functions\expect( 'get_option' )->twice()->andReturn( '/%postname%/' );

		$this->assertEquals(
			'https://example.com/main-dishes/page/3/?orderby=title',
			( new Pagination( 3 ) )->get( 'https://example.com/main-dishes/?orderby=title', $this->optionsWithPages( 5 ), 'it_IT' )
		);
	}

	public function test_get_uses_the_query_string_for_an_unrewritten_post_type(): void {
		Functions\expect( 'get_option' )->once()->andReturn( '/%postname%/' );

		$this->assertEquals(
			'https://example.com/?post_type=book&paged=3',
			( new Pagination( 3 ) )->get( 'https://example.com/?post_type=book', $this->optionsWithPages( 5 ), 'it_IT' )
		);
	}

	public function test_get_uses_the_query_string_for_a_post_without_a_pretty_link(): void {
		Functions\expect( 'get_option' )->once()->andReturn( '/%postname%/' );

		$this->assertEquals(
			'https://example.com/?p=123&page=2',
			( new Pagination( 0, 2 ) )->get( 'https://example.com/?p=123', $this->optionsWithPages( 4 ), 'it_IT' )
		);
	}

	public function test_get_drops_the_trailing_slash_of_the_target_blog(): void {
		Functions\expect( 'get_option' )->twice()->andReturn( '/%postname%' );

		$this->assertEquals(
			'https://example.com/main-dishes/page/3',
			( new Pagination( 3 ) )->get( 'https://example.com/main-dishes/', $this->optionsWithPages( 5 ), 'it_IT' )
		);
	}

	public function test_get_adds_the_page_of_a_post(): void {
		Functions\expect( 'get_option' )->twice()->andReturn( '/%postname%/' );

		$this->assertEquals(
			'https://example.com/my-post/2/',
			( new Pagination( 0, 2 ) )->get( 'https://example.com/my-post/', $this->optionsWithPages( 4 ), 'it_IT' )
		);
	}

	public function test_get_uses_the_pagination_base_for_a_static_front_page(): void {
		Functions\expect( 'get_option' )->twice()->andReturn( '/%postname%/' );

		$this->assertEquals(
			'https://example.com/page/2/',
			( new Pagination( 0, 2 ) )->get( 'https://example.com/', $this->optionsWithPages( 4 ), 'it_IT' )
		);
	}

	public function test_get_respects_a_corrected_number_of_pages(): void {
		Functions\expect( 'get_option' )->twice()->andReturn( '/%postname%/' );
		Filters\expectApplied( Pagination::MSLS_MAX_PAGES_HOOK )->once()->andReturn( 9 );

		$this->assertEquals(
			'https://example.com/main-dishes/page/3/',
			( new Pagination( 3 ) )->get( 'https://example.com/main-dishes/', $this->optionsWithPages( 0 ), 'it_IT' )
		);
	}

	public function test_get_can_be_filtered(): void {
		Functions\expect( 'get_option' )->twice()->andReturn( '/%postname%/' );
		Filters\expectApplied( Pagination::MSLS_GET_HOOK )->once()->andReturn( 'https://example.com/filtered/' );

		$this->assertEquals(
			'https://example.com/filtered/',
			( new Pagination( 3 ) )->get( 'https://example.com/main-dishes/', $this->optionsWithPages( 5 ), 'it_IT' )
		);
	}

	public function test_get_ignores_an_options_object_it_cannot_ask(): void {
		$options = \Mockery::mock( OptionsInterface::class );

		$url = 'https://example.com/main-dishes/';

		$this->assertEquals( $url, ( new Pagination( 3 ) )->get( $url, $options, 'it_IT' ) );
	}
}
