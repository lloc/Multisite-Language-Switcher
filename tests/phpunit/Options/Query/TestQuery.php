<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Query;

use Brain\Monkey\Functions;
use lloc\Msls\Db\SqlCacher;
use lloc\Msls\Options\Query\Author;
use lloc\Msls\Options\Query\Day;
use lloc\Msls\Options\Query\Month;
use lloc\Msls\Options\Query\PostType;
use lloc\Msls\Options\Query\Query;
use lloc\Msls\Options\Query\Year;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestQuery extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		global $wpdb;

		$wpdb = \Mockery::mock( '\wpdb' );
	}

	public function test_get_params(): void {
		$this->assertEquals( array(), Query::get_params() );
	}

	/**
	 * The order Query::create() asks WordPress in. Everything before the conditional that
	 * matches has to return false, so a case only has to name where the chain hits.
	 *
	 * @var array<int, string>
	 */
	private const CREATE_CHAIN = array( 'is_day', 'is_month', 'is_year', 'is_author', 'is_post_type_archive' );

	/**
	 * The third and fourth column are the getter Query::create() reads the archive from
	 * and how often it is called.
	 *
	 * @return array<string, array{?string, ?string, int, array<int, mixed>, ?class-string}>
	 */
	public static function create_provider(): array {
		return array(
			'day archive'         => array( 'is_day', 'get_query_var', 6, array( 1969, 6, 26 ), Day::class ),
			'month archive'       => array( 'is_month', 'get_query_var', 4, array( 1969, 6 ), Month::class ),
			'year archive'        => array( 'is_year', 'get_query_var', 2, array( 1969 ), Year::class ),
			'author archive'      => array( 'is_author', 'get_queried_object_id', 2, array( 42 ), Author::class ),
			'post type archive'   => array( 'is_post_type_archive', 'get_query_var', 2, array( 'book' ), PostType::class ),
			'no archive whatever' => array( null, null, 0, array(), null ),
		);
	}

	/**
	 * @param array<int, mixed> $values
	 * @param ?class-string     $expected
	 */
	#[DataProvider( 'create_provider' )]
	public function test_create( ?string $matching, ?string $getter, int $getter_times, array $values, ?string $expected ): void {
		foreach ( self::CREATE_CHAIN as $conditional ) {
			Functions\expect( $conditional )->once()->andReturn( $matching === $conditional );

			if ( $matching === $conditional ) {
				break;
			}
		}

		if ( null !== $getter ) {
			Functions\expect( $getter )->times( $getter_times )->andReturnValues( $values );
			Functions\expect( 'get_option' )->once();
		}

		if ( null === $expected ) {
			$this->assertNull( Query::create() );

			return;
		}

		$this->assertInstanceOf( $expected, Query::create() );
	}

	public function test_current_get_postlink(): void {
		$home_url = 'https://msls.co/';

		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );
		Functions\expect( 'home_url' )->once()->andReturn( $home_url );

		$sql_cache = \Mockery::mock( SqlCacher::class );

		$this->assertEquals( $home_url, ( new Query( $sql_cache ) )->get_postlink( 'de_DE' ) );
	}

	public function test_non_existent_get_postlink(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		$sql_cache = \Mockery::mock( SqlCacher::class );

		$this->assertEquals( '', ( new Query( $sql_cache ) )->get_postlink( 'fr_FR' ) );
	}

	public function test_get_permalink_returns_empty_when_no_translation(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		$sql_cache = \Mockery::mock( SqlCacher::class );

		$this->assertSame( '', ( new Query( $sql_cache ) )->get_permalink( 'fr_FR' ) );
	}
}
