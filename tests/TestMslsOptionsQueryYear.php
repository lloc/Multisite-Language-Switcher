<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryYear;
use lloc\Msls\MslsSqlCacher;

class TestMslsOptionsQueryYear extends MslsUnitTestCase {

	protected function get_test( int $year ): MslsOptionsQueryYear {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->once()->andReturn( $year );

		$sql_cacher = \Mockery::mock( MslsSqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( random_int( 0, 10 ) );

		return new MslsOptionsQueryYear( $sql_cacher );
	}

	public function test_has_value_true(): void {
		$this->assertTrue( $this->get_test( 1998 )->has_value( 'de_DE' ) );
	}

	public function test_has_value_false(): void {
		$this->assertFalse( $this->get_test( 0 )->has_value( 'de_DE' ) );
	}

	public function test_get_current_link_method(): void {
		Functions\expect( 'get_year_link' )->once()->andReturn( 'https://example.org/queried-year' );

		$this->assertEquals( 'https://example.org/queried-year', $this->get_test( 2015 )->get_current_link() );
	}
}
