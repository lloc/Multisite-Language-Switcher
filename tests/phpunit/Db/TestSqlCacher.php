<?php declare( strict_types=1 );

namespace lloc\MslsTests\Db;

use Brain\Monkey\Functions;
use lloc\Msls\Db\SqlCacher;
use lloc\MslsTests\MslsUnitTestCase;

final class TestSqlCacher extends MslsUnitTestCase {

	public function test_create(): void {
		global $wpdb;

		$wpdb = \Mockery::mock( \WPDB::class );

		$this->assertInstanceOf( SqlCacher::class, SqlCacher::create( 'MslsSqlCacherTest', '' ) );
		$this->assertInstanceOf( SqlCacher::class, SqlCacher::create( 'MslsSqlCacherTest', 'abc' ) );
		$this->assertInstanceOf( SqlCacher::class, SqlCacher::create( 'MslsSqlCacherTest', array() ) );
		$this->assertInstanceOf( SqlCacher::class, SqlCacher::create( 'MslsSqlCacherTest', array( 'abc', 'def' ) ) );
	}

	public function test_set_params_method(): void {
		global $wpdb;

		$wpdb = \Mockery::mock( \WPDB::class );

		$wpdb->shouldReceive( 'prepare' )->andReturn( '' );
		$wpdb->shouldReceive( 'get_results' )->andReturn( array() );

		Functions\when( 'wp_cache_get' )->justReturn( false );
		Functions\when( 'wp_cache_set' )->justReturn( true );

		$test = new SqlCacher( $wpdb, 'MslsSqlCacherTest' );

		$sql = $test->prepare(
			"SELECT blog_id FROM {$test->blogs} WHERE blog_id != %d AND site_id = %d",
			$test->blogid,
			$test->siteid
		);

		$this->assertIsSTring( $sql );
		$this->assertIsArray( $test->get_results( $sql ) );
	}
}
