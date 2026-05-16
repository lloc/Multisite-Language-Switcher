<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Query;

use Brain\Monkey\Functions;
use lloc\Msls\Db\SqlCacher;
use lloc\Msls\Options\Query\Day;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestDay extends MslsUnitTestCase {

	private function OptionsQueryDayFactory( int $year, int $monthnum, int $day ): Day {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->times( 3 )->andReturn( $year, $monthnum, $day );

		$sql_cacher = \Mockery::mock( SqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( random_int( 1, 10 ) );

		return new Day( $sql_cacher );
	}

	public function test_has_value_true(): void {
		$this->assertTrue( $this->OptionsQueryDayFactory( 1998, 12, 31 )->has_value( 'de_DE' ) );
	}

	public function test_has_value(): void {
		$this->assertFalse( $this->OptionsQueryDayFactory( 0, 0, 0 )->has_value( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_day_link' )->once()->andReturn( 'https://msls.co/queried-day' );

		$this->assertEquals( 'https://msls.co/queried-day', $this->OptionsQueryDayFactory( 2015, 07, 02 )->get_current_link() );
	}
}
