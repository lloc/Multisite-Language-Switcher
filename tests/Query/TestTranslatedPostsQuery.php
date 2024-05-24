<?php declare( strict_types=1 );

namespace lloc\MslsTests\Query;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;

use lloc\MslsTests\MslsUnitTestCase;
use lloc\Msls\Query\TranslatedPostsQuery;
use lloc\Msls\MslsSqlCacher;

class TestTranslatedPostsQuery extends MslsUnitTestCase {

	public function test_invoke_empty_string(): void {
		$sql_cache = \Mockery::mock( MslsSqlCacher::class );

		$this->assertEquals( array(), ( new TranslatedPostsQuery( $sql_cache ) )( '' ) );
	}

	public function test_invoke_with_string(): void {
		$result = array(
			(object) array(
				'option_id'   => 1,
				'option_name' => 'msls_123',
			),
		);

		$sql_cache = \Mockery::mock( MslsSqlCacher::class );
		$sql_cache->shouldReceive( 'prepare' )->once();
		$sql_cache->shouldReceive( 'get_results' )->once()->andReturn( $result );

		$this->assertEquals( $result, ( new TranslatedPostsQuery( $sql_cache ) )( 'de_DE' ) );
	}
}
