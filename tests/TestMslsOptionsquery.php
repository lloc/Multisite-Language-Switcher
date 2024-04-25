<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQuery;

/**
 * TestMslsOptionsQuery
 */
class TestMslsOptionsQuery extends Msls_UnitTestCase {

	public function test_create(): void {
		Functions\expect( 'is_day' )->once()->andReturn( false );
		Functions\expect( 'is_month' )->once()->andReturn( false );
		Functions\expect( 'is_year' )->once()->andReturn( false );
		Functions\expect( 'is_author' )->once()->andReturn( false );
		Functions\expect( 'is_post_type_archive' )->once()->andReturn( false );

		$this->assertNull( MslsOptionsQuery::create() );
	}

	public function test_current_get_postlink(): void {
		$home_url = 'https://example.org/';

		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );
		Functions\expect( 'home_url' )->once()->andReturn( $home_url );

		$this->assertEquals( $home_url, ( new MslsOptionsQuery() )->get_postlink( 'de_DE' ) );
	}

	public function test_non_existent_get_postlink(): void {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->assertEquals( '', ( new MslsOptionsQuery() )->get_postlink( 'fr_FR' ) );
	}

}