<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options;

use Brain\Monkey\Functions;
use lloc\Msls\Admin\Icon as MslsAdminIcon;
use lloc\Msls\ContentTypes\PostType;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;
use lloc\MslsTests\WP_Query;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestOptions extends MslsUnitTestCase {

	private function MslsOptionsFactory(): Options {
		Functions\when( 'home_url' )->justReturn( 'https://lloc.de' );
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'update_option' )->justReturn( true );

		return new Options();
	}

	/**
	 * @return array<string, array{bool, int, int, int}>
	 */
	public static function max_pages_provider(): array {
		return array(
			'the paginated blog index'              => array( true, 25, 10, 3 ),
			'an exact number of pages'              => array( true, 20, 10, 2 ),
			'a blog without posts'                  => array( true, 0, 10, 0 ),
			'a request which is not the blog index' => array( false, 25, 10, 0 ),
		);
	}

	#[DataProvider( 'max_pages_provider' )]
	public function test_get_max_pages( bool $is_home, int $count, int $per_page, int $expected ): void {
		$test = $this->MslsOptionsFactory();

		Functions\when( 'is_home' )->justReturn( $is_home );
		Functions\when( 'is_search' )->justReturn( false );
		Functions\when( 'get_option' )->justReturn( $per_page );
		Functions\when( 'wp_count_posts' )->justReturn( (object) array( 'publish' => $count ) );

		$this->assertEquals( $expected, $test->get_max_pages( 'de_DE', Options::PAGINATION_ARCHIVE ) );
	}

	public function test_get_max_pages_of_a_paginated_search(): void {
		$test = $this->MslsOptionsFactory();

		Functions\when( 'is_home' )->justReturn( false );
		Functions\when( 'is_search' )->justReturn( true );
		Functions\when( 'get_search_query' )->justReturn( 'pasta' );
		Functions\when( 'get_option' )->justReturn( 10 );

		WP_Query::$next_found_posts = 25;

		$this->assertEquals( 3, $test->get_max_pages( 'de_DE', Options::PAGINATION_ARCHIVE ) );
		$this->assertEquals( 'pasta', WP_Query::$last_args['s'] );
	}

	public function test_get_max_pages_of_a_search_without_a_term(): void {
		$test = $this->MslsOptionsFactory();

		Functions\when( 'is_home' )->justReturn( false );
		Functions\when( 'is_search' )->justReturn( true );
		Functions\when( 'get_search_query' )->justReturn( '' );
		Functions\when( 'get_option' )->justReturn( 10 );

		$this->assertEquals( 0, $test->get_max_pages( 'de_DE', Options::PAGINATION_ARCHIVE ) );
	}

	public function test_get_max_pages_of_a_static_front_page(): void {
		$test = $this->MslsOptionsFactory();

		$post               = \Mockery::mock( '\WP_Post' );
		$post->post_content = 'One<!--nextpage-->Two<!--nextpage-->Three';

		Functions\when( 'is_front_page' )->justReturn( true );
		Functions\when( 'get_option' )->justReturn( 17 );
		Functions\expect( 'get_post' )->once()->with( 17 )->andReturn( $post );

		$this->assertEquals( 3, $test->get_max_pages( 'de_DE', Options::PAGINATION_SINGLE ) );
	}

	public function test_get_max_pages_of_a_blog_without_a_static_front_page(): void {
		$test = $this->MslsOptionsFactory();

		Functions\when( 'is_front_page' )->justReturn( true );
		Functions\when( 'get_option' )->justReturn( 0 );
		Functions\expect( 'get_post' )->never();

		$this->assertEquals( 0, $test->get_max_pages( 'de_DE', Options::PAGINATION_SINGLE ) );
	}

	public function test_get_max_pages_of_a_split_post_outside_the_front_page(): void {
		$test = $this->MslsOptionsFactory();

		Functions\when( 'is_front_page' )->justReturn( false );
		Functions\expect( 'get_post' )->never();

		$this->assertEquals( 0, $test->get_max_pages( 'de_DE', Options::PAGINATION_SINGLE ) );
	}

	public function test_is_main_page(): void {
		Functions\when( 'is_front_page' )->justReturn( true );

		$this->assertIsBool( Options::is_main_page() );
	}

	public function test_is_tax_page(): void {
		Functions\when( 'is_category' )->justReturn( true );

		$this->assertIsBool( Options::is_tax_page() );
	}

	public function test_is_query_page(): void {
		Functions\when( 'is_date' )->justReturn( true );

		$this->assertIsBool( Options::is_query_page() );
	}

	public function test_create(): void {
		$post_type = \Mockery::mock( PostType::class );
		$post_type->shouldReceive( 'is_taxonomy' )->once()->andReturnFalse();

		Functions\expect( 'msls_content_types' )->once()->andReturn( $post_type );

		Functions\expect( 'is_admin' )->once()->andReturnTrue();
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$this->assertInstanceOf( Options::class, Options::create() );
	}

	public function test_get_arg(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertNull( $obj->get_arg( 0 ) );
		$this->assertIsSTring( $obj->get_arg( 0, '' ) );
		$this->assertIsFloat( $obj->get_arg( 0, 1.1 ) );
		$this->assertIsArray( $obj->get_arg( 0, array() ) );
	}

	public function test_save(): void {
		$arr = array(
			'de_DE' => 1,
			'it_IT' => 2,
		);

		Functions\expect( 'delete_option' )->once()->with( 'msls' );
		Functions\expect( 'add_option' )->once()->with( 'msls', $arr, '', true );

		$obj = $this->MslsOptionsFactory();

		$this->expectNotToPerformAssertions();
		$obj->save( $arr );
	}

	/**
	 * @return array<string, array{bool, mixed}>
	 */
	public static function set_provider(): array {
		return array(
			'empty array'  => array( true, array() ),
			'filled array' => array(
				true,
				array(
					'temp' => 'abc',
					'en'   => 1,
					'us'   => 2,
				),
			),
			'string'       => array( false, 'Test' ),
			'integer'      => array( false, 1 ),
			'float'        => array( false, 1.1 ),
			'null'         => array( false, null ),
			'object'       => array( false, new \stdClass() ),
		);
	}

	#[DataProvider( 'set_provider' )]
	function test_set( $expected, $input ): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertEquals( $expected, $obj->set( $input ) );
	}

	function test_get_permalink(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertIsSTring( $obj->get_permalink( 'de_DE' ) );
	}

	function test_get_postlink(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertIsSTring( $obj->get_postlink( 'de_DE' ) );
		$this->assertEquals( '', $obj->get_postlink( 'de_DE' ) );
	}

	function test_get_current_link(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertIsSTring( $obj->get_current_link() );
	}

	function test_is_excluded(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertIsBool( $obj->is_excluded() );
	}

	function test_is_content_filter(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertIsBool( $obj->is_content_filter() );
	}

	function test_get_order(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertIsSTring( $obj->get_order() );
	}

	function test_get_url(): void {
		Functions\when( 'plugins_url' )->justReturn( 'https://msls.co/wp-content/plugins' );

		$obj = $this->MslsOptionsFactory();

		$this->assertIsSTring( $obj->get_url( '/dev/test' ) );
	}

	function test_get_flag_url(): void {
		Functions\when( 'is_admin' )->justReturn( true );
		Functions\when( 'plugins_url' )->justReturn( 'https://msls.co/wp-content/plugins' );
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 2 ) . '/' );

		$obj = $this->MslsOptionsFactory();

		$this->assertIsSTring( $obj->get_flag_url( 'de_DE' ) );
	}

	function test_get_available_languages(): void {
		Functions\expect( 'get_available_languages' )->once()->andReturn( array( 'de_DE', 'it_IT' ) );
		Functions\expect( 'format_code_lang' )->atLeast()->once()->andReturnUsing(
			function ( $code ) {
				$map = array(
					'de_DE' => 'German',
					'it_IT' => 'Italian',
				);
				return $map[ $code ] ?? 'American English';
			}
		);

		$obj = $this->MslsOptionsFactory();

		$expected = array(
			'en_US' => 'American English',
			'de_DE' => 'German',
			'it_IT' => 'Italian',
		);
		$this->assertEquals( $expected, $obj->get_available_languages() );
	}

	public function test_get_icon_type_standard(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertEquals( MslsAdminIcon::TYPE_FLAG, $obj->get_icon_type() );
	}

	public function test_get_icon_type_admin_display(): void {
		$obj = $this->MslsOptionsFactory();
		$obj->set( array( 'admin_display' => MslsAdminIcon::TYPE_LABEL ) );

		$this->assertEquals( MslsAdminIcon::TYPE_LABEL, $obj->get_icon_type() );
	}

	/**
	 * The columns after $expected are $with_front, $is_subdomain_install, $using_permalinks,
	 * $permalink_structure and $is_main_site.
	 *
	 * The two 'blogg' rows differ only in $is_main_site: the lookalike slug is never
	 * stripped, so that flag cannot change the result. They are kept as a pair on purpose,
	 * to pin down that the main site does not get the prefix re-added either.
	 *
	 * @return array<string, array{string|null, string, bool, bool, bool, string, bool}>
	 */
	public static function slug_check_provider(): array {
		return array(
			'empty url returns early'                     => array( '', '', false, false, false, '', false ),
			'null url returns early'                      => array( null, '', false, false, false, '', false ),
			'subdomain install without permalinks'        => array( 'https://msls.co/blog/test', 'https://msls.co/blog/test', true, true, false, '', false ),
			'permalinks without subdomain install'        => array( 'https://msls.co/blog/test', 'https://msls.co/blog/test', true, false, true, '', false ),
			'no permalink structure'                      => array( 'https://msls.co/blog/test', 'https://msls.co/blog/test', true, true, true, '', false ),
			'dated structure without front, sub site'     => array( 'https://msls.co/blog/2024/05/test', 'https://msls.co/blog/2024/05/test', true, true, true, '/%year%/%monthnum%/%postname%/', false ),
			'dated structure with front, sub site'        => array( 'https://msls.co/blog/2024/05/test', 'https://msls.co/2024/05/test', true, true, true, '/blog/%year%/%monthnum%/%postname%/', false ),
			'postname structure without front, sub site'  => array( 'https://msls.co/blog/test', 'https://msls.co/blog/test', true, true, true, '/%postname%/', false ),
			'postname structure with front, sub site'     => array( 'https://msls.co/blog/test', 'https://msls.co/test', true, true, true, '/blog/%postname%/', false ),
			'dated structure without front, main site'    => array( 'https://msls.co/blog/2024/05/test', 'https://msls.co/blog/2024/05/test', true, true, true, '/%year%/%monthnum%/%postname%/', true ),
			'dated structure with front, main site'       => array( 'https://msls.co/blog/2024/05/test', 'https://msls.co/blog/2024/05/test', true, true, true, '/blog/%year%/%monthnum%/%postname%/', true ),
			'postname structure without front, main site' => array( 'https://msls.co/blog/test', 'https://msls.co/blog/test', true, true, true, '/%postname%/', true ),
			'postname structure with front, main site'    => array( 'https://msls.co/blog/test', 'https://msls.co/blog/test', true, true, true, '/blog/%postname%/', true ),
			'lookalike slug is left alone, sub site'      => array( 'https://msls.co/blogg/', 'https://msls.co/blogg/', true, true, true, '/blog/%postname%/', false ),
			'lookalike slug is left alone, main site'     => array( 'https://msls.co/blogg/', 'https://msls.co/blogg/', true, true, true, '/blog/%postname%/', true ),
			'bare front is stripped down to the home url' => array( 'https://msls.co/blog/', 'https://msls.co/', true, true, true, '/blog/%postname%/', false ),
		);
	}

	#[DataProvider( 'slug_check_provider' )]
	public function test_check_for_blog_slug( ?string $url, string $expected, bool $with_front, bool $is_subdomain_install, bool $using_permalinks, string $permalink_structure, bool $is_main_site ): void {
		global $wp_rewrite, $current_site;

		$options             = \Mockery::mock( Options::class );
		$options->with_front = $with_front;
		$wp_rewrite          = \Mockery::mock( '\WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'using_permalinks' )->andReturn( $using_permalinks );
		$current_site          = \Mockery::mock( '\WP_Network' );
		$current_site->blog_id = 1;

		Functions\when( 'is_subdomain_install' )->justReturn( $is_subdomain_install );
		Functions\expect( 'home_url' )->andReturnUsing(
			function ( $url = '' ) {
				return 'https://msls.co' . $url;
			}
		);
		Functions\when( 'get_blog_option' )->justReturn( $permalink_structure );
		Functions\when( 'is_main_site' )->justReturn( $is_main_site );

		$this->assertEquals( $expected, Options::check_for_blog_slug( $url, $options ) );
	}

	public function test_get_slug(): void {
		$obj = $this->MslsOptionsFactory();

		$this->assertEquals( '', $obj->get_slug( 'post' ) );
	}

	public function test_get_option_name(): void {
		$this->assertSame( 'msls', $this->MslsOptionsFactory()->get_option_name() );
	}
}
