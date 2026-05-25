<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Query;

use Brain\Monkey\Functions;
use lloc\Msls\Db\SqlCacher;
use lloc\Msls\Options\Query\Year;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestOptionsQueryYear extends MslsUnitTestCase {

	private function OptionsQueryYearFactory( int $year ): Year {
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->once()->andReturn( $year );

		$sql_cacher = \Mockery::mock( SqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( random_int( 1, 10 ) );

		return new Year( $sql_cacher );
	}

	public function test_has_value_true(): void {
		$this->assertTrue( $this->OptionsQueryYearFactory( 1998 )->has_value( 'de_DE' ) );
	}

	public function test_has_value_false(): void {
		$this->assertFalse( $this->OptionsQueryYearFactory( 0 )->has_value( 'de_DE' ) );
	}

	public function test_get_current_link_method(): void {
		Functions\expect( 'get_year_link' )->once()->andReturn( 'https://msls.co/queried-year' );

		$test = $this->OptionsQueryYearFactory( 2015 );

		$this->assertEquals( 'https://msls.co/queried-year', $test->get_current_link() );
	}
}
