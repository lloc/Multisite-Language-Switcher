<?php declare( strict_types=1 );

namespace lloc\MslsTests\Blog;

use Brain\Monkey\Functions;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestCollection extends MslsUnitTestCase {

	const TOTAL_USERS = 3210;

	/**
	 * Captures the last user-id argument passed to the mocked
	 * `get_blogs_of_user()`. Reset in setUp(); read by individual tests that
	 * verify the int cast in `Collection::get_blogs_of_reference_user()`.
	 *
	 * @var mixed
	 */
	protected $captured_user_id = null;

	protected function setUp(): void {
		parent::setUp();

		$this->captured_user_id = null;

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_order' )->andReturn( 'description' );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );
		$options->shouldReceive( 'has_value' )->andReturn( false );

		Functions\expect( 'msls_options' )->atLeast()->once()->andReturn( $options );

		$a = \Mockery::mock( Blog::class );
		$b = \Mockery::mock( Blog::class );
		$c = \Mockery::mock( Blog::class );

		$a->userblog_id = 1;
		$b->userblog_id = 2;
		$c->userblog_id = 3;

		Functions\expect( 'get_current_blog_id' )->atLeast()->once()->andReturn( 1 );
		Functions\expect( 'get_users' )->atLeast()->once()->andReturn( array() );
		Functions\expect( 'get_blogs_of_user' )->atLeast()->once()->andReturnUsing(
			function ( $user_id ) use ( $a, $b, $c ) {
				$this->captured_user_id = $user_id;
				return array( $a, $b, $c );
			}
		);

		Functions\expect( 'get_blog_option' )->atLeast()->once()->andReturnUsing(
			function ( $blog_id, $option ) {
				$wplang = array(
					1 => 'de_DE',
					2 => 'it_IT',
					3 => 'fr_FR',
				);

				$msls = array(
					1 => array( 'description' => 'Deutsch' ),
					2 => array( 'description' => 'Italiano' ),
					3 => array( 'description' => 'Français' ),
				);

				switch ( $option ) {
					case 'active_plugins':
						$value = in_array(
							$blog_id,
							array( 1, 2 )
						) ? array( 'multisite-language-switcher/MultisiteLanguageSwitcher.php' ) : array();
						break;
					case 'WPLANG':
						$value = $wplang[ $blog_id ] ?? false;
						break;
					case 'msls':
							$value = $msls[ $blog_id ] ?? false;
						break;
				}

				return $value;
			}
		);
	}

	/**
	 * The second column is the fallback description, false where the call passes none.
	 *
	 * @return array<string, array{int, string|false, string|false}>
	 */
	public static function configured_blog_description_provider(): array {
		return array(
			'unknown blog falls back to the given description' => array( 0, 'Test', 'Test' ),
			'german blog'                     => array( 1, false, 'Deutsch' ),
			'italian blog'                    => array( 2, false, 'Italiano' ),
			'french blog'                     => array( 3, false, 'Français' ),
			'unknown blog without a fallback' => array( 4, false, false ),
		);
	}

	/**
	 * @param string|false $description
	 * @param string|false $expected
	 */
	#[DataProvider( 'configured_blog_description_provider' )]
	public function test_get_configured_blog_description( int $blog_id, $description, $expected ): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertSame( $expected, $obj->get_configured_blog_description( $blog_id, $description ) );
	}

	public function test_get_blogs_of_reference_user(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'has_value' )->andReturn( true );

		$obj = new Collection();

		$this->assertIsArray( $obj->get_blogs_of_reference_user( $options ) );
	}

	public function test_get_blogs_of_reference_user_casts_string_reference_user_to_int(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$options                 = \Mockery::mock( Options::class );
		$options->reference_user = '5';
		$options->shouldReceive( 'has_value' )->with( 'reference_user' )->andReturn( true );

		$obj = new Collection();
		$obj->get_blogs_of_reference_user( $options );

		$this->assertSame( 5, $this->captured_user_id );
	}

	public function test_get_blogs_of_reference_user_empty_fallback_is_safe(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'has_value' )->with( 'reference_user' )->andReturn( false );

		$obj = new Collection();
		$obj->get_blogs_of_reference_user( $options );

		$this->assertSame( 0, $this->captured_user_id );
	}

	public function test_get_current_blog_id(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertIsInt( $obj->get_current_blog_id() );
	}

	public function test_has_current_blog(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertIsBool( $obj->has_current_blog() );
	}

	public function test_is_current_blog_true(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$blog              = \Mockery::mock( Blog::class );
		$blog->userblog_id = 1;

		$this->assertTrue( $obj->is_current_blog( $blog ) );
	}

	public function test_is_current_blog_false(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$blog              = \Mockery::mock( Blog::class );
		$blog->userblog_id = 2;

		$this->assertFalse( $obj->is_current_blog( $blog ) );
	}

	public function test_get_objects(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertIsArray( $obj->get_objects() );
	}

	public function test_get_object(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertInstanceOf( Blog::class, $obj->get_object( 1 ) );
		$this->assertNull( $obj->get_object( 4 ) );
	}

	public function test_is_plugin_active_networkwide(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn(
			array( 'multisite-language-switcher/MultisiteLanguageSwitcher.php' => 'Multisite Language Switcher' )
		);

		$obj = new Collection();

		$this->assertTrue( $obj->is_plugin_active( 4 ) );
	}

	/**
	 * @return array<string, array{int, bool}>
	 */
	public static function is_plugin_active_provider(): array {
		return array(
			'configured blog'   => array( 1, true ),
			'second blog'       => array( 2, true ),
			'unconfigured blog' => array( 3, false ),
		);
	}

	#[DataProvider( 'is_plugin_active_provider' )]
	public function test_is_plugin_active( int $blog_id, bool $expected ): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertSame( $expected, $obj->is_plugin_active( $blog_id ) );
	}

	public function test_get_plugin_active_blogs(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertIsArray( $obj->get_plugin_active_blogs() );
	}

	public function test_get(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertIsArray( $obj->get() );
	}

	public function test_get_filtered(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertIsArray( $obj->get_filtered() );
		$this->assertIsArray( $obj->get_filtered( true ) );
	}

	public function test_get_users_single(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertIsArray( $obj->get_users( array( 'ID' ), 1 ) );
	}

	public function test_get_users_massive(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );
		Functions\expect( 'count_users' )->never();

		$obj = new Collection();

		$this->assertIsArray( $obj->get_users( array( 'ID' ), self::TOTAL_USERS ) );
	}

	public function test_get_current_blog(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertInstanceOf( Blog::class, $obj->get_current_blog() );
	}

	/**
	 * A null blog id means the call passes no argument at all.
	 *
	 * @return array<string, array{?int, string}>
	 */
	public static function blog_language_provider(): array {
		return array(
			'german blog'             => array( 1, 'de_DE' ),
			'italian blog'            => array( 2, 'it_IT' ),
			'french blog'             => array( 3, 'fr_FR' ),
			'current blog by default' => array( null, 'de_DE' ),
		);
	}

	#[DataProvider( 'blog_language_provider' )]
	public function test_get_blog_language( ?int $blog_id, string $expected ): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertSame( $expected, $obj->get_blog_language( $blog_id ) );
	}

	/**
	 * @return array<string, array{string, ?int}>
	 */
	public static function blog_id_provider(): array {
		return array(
			'german'          => array( 'de_DE', 1 ),
			'italian'         => array( 'it_IT', 2 ),
			'not a msls blog' => array( 'fr_FR', null ),
		);
	}

	#[DataProvider( 'blog_id_provider' )]
	public function test_get_blog_id( string $language, ?int $expected ): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new Collection();

		$this->assertSame( $expected, $obj->get_blog_id( $language ) );
	}
}
