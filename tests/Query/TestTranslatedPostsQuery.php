<?php declare( strict_types=1 );

namespace lloc\MslsTests\Query;

use lloc\MslsTests\MslsUnitTestCase;
use lloc\Msls\Query\TranslatedPostIdQuery;
use lloc\Msls\MslsSqlCacher;

class TestTranslatedPostsQuery extends MslsUnitTestCase {

	public function test_invoke_empty_string(): void {
		$sql_cache = \Mockery::mock( MslsSqlCacher::class );

		$this->assertEquals( array(), ( new TranslatedPostIdQuery( $sql_cache ) )( '' ) );
	}

	public function test_invoke_with_string(): void {
		$result = array(
			(object) array(
				'option_id'   => 1,
				'option_name' => 'msls_123',
			),
			(object) array(
				'option_id'   => 2,
				'option_name' => 'msls_42',
			),
			(object) array(
				'option_id'   => 3,
				'option_name' => 'msls_17',
			),
		);

		$sql_cache = \Mockery::mock( MslsSqlCacher::class );
		$sql_cache->shouldReceive( 'prepare' )->once();
		$sql_cache->shouldReceive( 'get_results' )->once()->andReturn( $result );

		$this->assertEquals( array( '123', '42', '17' ), ( new TranslatedPostIdQuery( $sql_cache ) )( 'de_DE' ) );
	}
}
