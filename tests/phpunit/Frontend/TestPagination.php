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

	/**
	 * Columns: paged, page, url, max pages of the target blog, get_option() calls, permalink structure, expected url.
	 *
	 * @return array<string, array{int, int, string, int, int, string, string}>
	 */
	public static function get_provider(): array {
		return array(
			'returns an empty url untouched'              => array( 3, 0, '', 5, 0, '', '' ),
			'leaves an unpaginated request alone'         => array( 0, 0, 'https://example.com/main-dishes/', 5, 0, '', 'https://example.com/main-dishes/' ),
			'adds the page of an archive'                 => array( 3, 0, 'https://example.com/main-dishes/', 5, 2, '/%postname%/', 'https://example.com/main-dishes/page/3/' ),
			'falls back to the first page when the blog is shorter' => array( 3, 0, 'https://example.com/main-dishes/', 2, 0, '', 'https://example.com/main-dishes/' ),
			'uses the query string without pretty permalinks' => array( 3, 0, 'https://example.com/?cat=7', 5, 1, '', 'https://example.com/?cat=7&paged=3' ),
			'keeps a query string behind the page'        => array( 3, 0, 'https://example.com/main-dishes/?orderby=title', 5, 2, '/%postname%/', 'https://example.com/main-dishes/page/3/?orderby=title' ),
			'uses the query string for an unrewritten post type' => array( 3, 0, 'https://example.com/?post_type=book', 5, 1, '/%postname%/', 'https://example.com/?post_type=book&paged=3' ),
			'uses the query string for a post without a pretty link' => array( 0, 2, 'https://example.com/?p=123', 4, 1, '/%postname%/', 'https://example.com/?p=123&page=2' ),
			'drops the trailing slash of the target blog' => array( 3, 0, 'https://example.com/main-dishes/', 5, 2, '/%postname%', 'https://example.com/main-dishes/page/3' ),
			'adds the page of a post'                     => array( 0, 2, 'https://example.com/my-post/', 4, 2, '/%postname%/', 'https://example.com/my-post/2/' ),
			'uses the pagination base for a static front page' => array( 0, 2, 'https://example.com/', 4, 2, '/%postname%/', 'https://example.com/page/2/' ),
		);
	}

	#[DataProvider( 'get_provider' )]
	public function test_get( int $paged, int $page, string $url, int $max_pages, int $get_option_calls, string $permalink_structure, string $expected ): void {
		Functions\expect( 'get_option' )->times( $get_option_calls )->andReturn( $permalink_structure );

		$this->assertEquals( $expected, ( new Pagination( $paged, $page ) )->get( $url, $this->optionsWithPages( $max_pages ), 'it_IT' ) );
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
