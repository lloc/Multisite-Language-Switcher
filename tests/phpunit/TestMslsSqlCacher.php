<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsSqlCacher;

final class TestMslsSqlCacher extends MslsUnitTestCase {

	public function test_create(): void {
		global $wpdb;

		$wpdb = \Mockery::mock( \WPDB::class );

		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', '' ) );
		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', 'abc' ) );
		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', array() ) );
		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', array( 'abc', 'def' ) ) );
	}

	public function test_set_params_method(): void {
		global $wpdb;

		$wpdb = \Mockery::mock( \WPDB::class );

		$wpdb->shouldReceive( 'prepare' )->andReturn( '' );
		$wpdb->shouldReceive( 'get_results' )->andReturn( array() );

		Functions\when( 'wp_cache_get' )->justReturn( false );
		Functions\when( 'wp_cache_set' )->justReturn( true );

		$test = new MslsSqlCacher( $wpdb, 'MslsSqlCacherTest' );

		$sql = $test->prepare(
			"SELECT blog_id FROM {$test->blogs} WHERE blog_id != %d AND site_id = %d",
			$test->blogid,
			$test->siteid
		);

		$this->assertIsSTring( $sql );
		$this->assertIsArray( $test->get_results( $sql ) );
	}
}
