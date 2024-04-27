<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsSqlCacher;

class TestMslsSqlCacher extends MslsUnitTestCase {

	public function get_sut(): MslsSqlCacher {
		$wpdb = \Mockery::mock( \WPDB::class );
		$wpdb->shouldReceive( [
			'prepare'     => '',
			'get_results' => []
		] );

		return new MslsSqlCacher( $wpdb, 'MslsSqlCacherTest' );
	}

	public function test_set_params_method(): void {
		Functions\when( 'wp_cache_get' )->justReturn( false );
		Functions\when( 'wp_cache_set' )->justReturn( true );

		$obj = $this->get_sut();

		$this->assertInstanceOf( MslsSqlCacher::class, $obj->set_params( array( 'Cache', 'Test' ) ) );
		$this->assertIsSTring( $obj->get_key() );
		$this->assertEquals( 'MslsSqlCacherTest_Cache_Test', $obj->get_key() );

		$this->assertInstanceOf( MslsSqlCacher::class, $obj->set_params( 'Cache_Test' ) );
		$this->assertIsSTring( $obj->get_key() );
		$this->assertEquals( 'MslsSqlCacherTest_Cache_Test', $obj->get_key() );

		$sql = $obj->prepare(
			"SELECT blog_id FROM {$obj->blogs} WHERE blog_id != %d AND site_id = %d",
			$obj->blogid,
			$obj->siteid
		);
		$this->assertIsSTring( $sql );
		$this->assertIsArray( $obj->get_results( $sql ) );
	}

}
