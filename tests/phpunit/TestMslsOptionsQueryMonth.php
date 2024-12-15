<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsQueryMonth;
use lloc\Msls\MslsSqlCacher;

final class TestMslsOptionsQueryMonth extends MslsUnitTestCase {

	private function MslsOptionsQueryMonthFactory( int $year, int $monthnum ): MslsOptionsQueryMonth {
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->times( 2 )->andReturn( $year, $monthnum );

		$sql_cacher = \Mockery::mock( MslsSqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( random_int( 1, 10 ) );

		return new MslsOptionsQueryMonth( $sql_cacher );
	}

	public function test_has_value_true(): void {
		$this->assertTrue( $this->MslsOptionsQueryMonthFactory( 1998, 12 )->has_value( 'de_DE' ) );
	}

	public function test_has_value_false(): void {
		$this->assertFalse( $this->MslsOptionsQueryMonthFactory( 0, 0 )->has_value( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_month_link' )->once()->andReturn( 'https://msls.co/queried-month' );

		$this->assertEquals( 'https://msls.co/queried-month', $this->MslsOptionsQueryMonthFactory( 2015, 7 )->get_current_link() );
	}
}
