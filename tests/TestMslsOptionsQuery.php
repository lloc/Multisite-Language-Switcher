<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQuery;
use lloc\Msls\MslsOptionsQueryAuthor;
use lloc\Msls\MslsOptionsQueryDay;
use lloc\Msls\MslsOptionsQueryMonth;
use lloc\Msls\MslsOptionsQueryPostType;
use lloc\Msls\MslsOptionsQueryYear;

/**
 * TestMslsOptionsQuery
 */
class TestMslsOptionsQuery extends MslsUnitTestCase {

	public function test_create_is_day(): void {
		Functions\expect( 'is_day' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->times( 3 )->andReturnValues( array( 1969, 6, 26 ) );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( MslsOptionsQueryDay::class, MslsOptionsQuery::create() );
	}
	public function test_create_is_month(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->twice()->andReturnValues( array( 1969, 6 ) );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( MslsOptionsQueryMonth::class, MslsOptionsQuery::create() );
	}

	public function test_create_is_year(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->once()->andReturn( 1969 );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( MslsOptionsQueryYear::class, MslsOptionsQuery::create() );
	}

	public function test_create_is_author(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( MslsOptionsQueryAuthor::class, MslsOptionsQuery::create() );
	}

	public function test_create_is_post_type_archive(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( true );
		Functions\expect( 'get_query_var' )->once()->andReturn( 'book' );
		Functions\expect( 'get_option' )->once();

		$this->assertInstanceOf( MslsOptionsQueryPostType::class, MslsOptionsQuery::create() );
	}

	public function test_create_is_null(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );

		$this->assertNull( MslsOptionsQuery::create() );
	}

	public function test_current_get_postlink(): void {
		$home_url = 'https://example.org/';

		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );
		Functions\expect( 'home_url' )->once()->andReturn( $home_url );

		$this->assertEquals( $home_url, ( new MslsOptionsQuery() )->get_postlink( 'de_DE' ) );
	}

	public function test_non_existent_get_postlink(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		$this->assertEquals( '', ( new MslsOptionsQuery() )->get_postlink( 'fr_FR' ) );
	}
}
