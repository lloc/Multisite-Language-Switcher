<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsSqlCacher;

class TestMslsSqlCacher extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		$wpdb = \Mockery::mock( \WPDB::class );

		$wpdb->shouldReceive( 'prepare' )->andReturn( '' );
		$wpdb->shouldReceive( 'get_results' )->andReturn( array() );

		$this->test = new MslsSqlCacher( $wpdb, 'MslsSqlCacherTest' );
	}

	public function test_create(): void {
		global $wpdb;

		$wpdb = \Mockery::mock( \WPDB::class );

		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', '' ) );
		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', 'abc' ) );
		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', array() ) );
		$this->assertInstanceOf( MslsSqlCacher::class, MslsSqlCacher::create( 'MslsSqlCacherTest', array( 'abc', 'def' ) ) );
	}

	public function test_set_params_method(): void {
		Functions\when( 'wp_cache_get' )->justReturn( false );
		Functions\when( 'wp_cache_set' )->justReturn( true );

		$sql = $this->test->prepare(
			"SELECT blog_id FROM {$this->test->blogs} WHERE blog_id != %d AND site_id = %d",
			$this->test->blogid,
			$this->test->siteid
		);

		$this->assertIsSTring( $sql );
		$this->assertIsArray( $this->test->get_results( $sql ) );
	}
}
