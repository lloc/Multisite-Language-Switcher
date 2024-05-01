<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsSqlCacher;

class TestMslsSqlCacher extends MslsUnitTestCase {

	protected function setUp(): void {
		$wpdb = \Mockery::mock( \WPDB::class );
		$wpdb->shouldReceive( [
			'prepare'     => '',
			'get_results' => []
		] );

		$this->test = new MslsSqlCacher( $wpdb, 'MslsSqlCacherTest' );
	}

	public function test_set_params_method(): void {
		Functions\when( 'wp_cache_get' )->justReturn( false );
		Functions\when( 'wp_cache_set' )->justReturn( true );

		$this->assertInstanceOf( MslsSqlCacher::class, $this->test->set_params( array( 'Cache', 'Test' ) ) );
		$this->assertIsSTring( $this->test->get_key() );
		$this->assertEquals( 'MslsSqlCacherTest_Cache_Test', $this->test->get_key() );

		$this->assertInstanceOf( MslsSqlCacher::class, $this->test->set_params( 'Cache_Test' ) );
		$this->assertIsSTring( $this->test->get_key() );
		$this->assertEquals( 'MslsSqlCacherTest_Cache_Test', $this->test->get_key() );

		$sql = $this->test->prepare(
			"SELECT blog_id FROM {$this->test->blogs} WHERE blog_id != %d AND site_id = %d",
			$this->test->blogid,
			$this->test->siteid
		);
		$this->assertIsSTring( $sql );
		$this->assertIsArray( $this->test->get_results( $sql ) );
	}

}
