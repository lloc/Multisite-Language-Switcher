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

use function Brain\Monkey\Functions;

final class TestQuery extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		global $wpdb;

		$wpdb = \Mockery::mock( '\wpdb' );
	}

	public function test_get_params(): void {
		$this->assertEquals( array(), Query::get_params() );
	}

	public function test_create_is_day(): void {
		Functions\expect( 'is_day' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->times( 6 )->andReturnValues( array( 1969, 6, 26 ) );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( Day::class, Query::create() );
	}
	public function test_create_is_month(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->times( 4 )->andReturnValues( array( 1969, 6 ) );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( Month::class, Query::create() );
	}

	public function test_create_is_year(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->times( 2 )->andReturn( 1969 );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( Year::class, Query::create() );
	}

	public function test_create_is_author(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object_id' )->times( 2 )->andReturn( 42 );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( Author::class, Query::create() );
	}

	public function test_create_is_post_type_archive(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->times( 2 )->andReturn( 'book' );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( PostType::class, Query::create() );
	}

	public function test_create_is_null(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );

		$this->assertNull( Query::create() );
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
