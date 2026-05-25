<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Query;

use Brain\Monkey\Functions;
use lloc\Msls\Db\SqlCacher;
use lloc\Msls\Options\Query\Month;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestMonth extends MslsUnitTestCase {

	private function OptionsQueryMonthFactory( int $year, int $monthnum ): Month {
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->times( 2 )->andReturn( $year, $monthnum );

		$sql_cacher = \Mockery::mock( SqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( random_int( 1, 10 ) );

		return new Month( $sql_cacher );
	}

	public function test_has_value_true(): void {
		$this->assertTrue( $this->OptionsQueryMonthFactory( 1998, 12 )->has_value( 'de_DE' ) );
	}

	public function test_has_value_false(): void {
		$this->assertFalse( $this->OptionsQueryMonthFactory( 0, 0 )->has_value( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_month_link' )->once()->andReturn( 'https://msls.co/queried-month' );

		$this->assertEquals( 'https://msls.co/queried-month', $this->OptionsQueryMonthFactory( 2015, 7 )->get_current_link() );
	}
}
