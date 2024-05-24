<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryDay;
use lloc\Msls\MslsSqlCacher;

/**
 * TestMslsOptionsQueryDay
 */
class TestMslsOptionsQueryDay extends MslsUnitTestCase {

	public function get_test( int $year, int $monthnum, int $day ): MslsOptionsQueryDay {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->times( 3 )->andReturn( $year, $monthnum, $day );

		$sql_cacher = \Mockery::mock( MslsSqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( random_int( 1, 10 ) );

		return new MslsOptionsQueryDay( $sql_cacher );
	}

	public function test_has_value_true(): void {
		$this->assertTrue( $this->get_test( 1998, 12, 31 )->has_value( 'de_DE' ) );
	}

	public function test_has_value(): void {
		$this->assertFalse( $this->get_test( 0, 0, 0 )->has_value( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_day_link' )->once()->andReturn( 'https://example.org/queried-day' );

		$this->assertEquals( 'https://example.org/queried-day', $this->get_test( 2015, 07, 02 )->get_current_link() );
	}
}
