<?php
/**
 * Tests for MslsSqlCacher
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsPostTag
 */
class WP_Test_MslsSqlCacher extends Msls_UnitTestCase {

	/**
	 * Verify the static init-method
 	 * @covers MslsSqlCacher::init
 	 * @covers MslsSqlCacher::__construct
	 */
	function test_init_method() {
		$obj = MslsSqlCacher::init( 'MslsSqlCacherTest' );
		$this->assertInstanceOf( 'MslsSqlCacher', $obj );
		return $obj;
	}

	/**
	 * Verify the rest of the methods
	 * @depends test_init_method
 	 * @covers MslsSqlCacher::set_params
 	 * @covers MslsSqlCacher::get_key
 	 * @covers MslsSqlCacher::__get
 	 * @covers MslsSqlCacher::__call
	 */
	function test_set_params_method( $obj ) {
		$this->assertInstanceOf( 'MslsSqlCacher', $obj->set_params( array( 'Cache', 'Test' ) ) );
		$this->assertInternalType( 'string', $obj->get_key() );
		$this->assertEquals( 'MslsSqlCacherTest_Cache_Test', $obj->get_key() );

		$this->assertInstanceOf( 'MslsSqlCacher', $obj->set_params( 'Cache_Test' ) );
		$this->assertInternalType( 'string', $obj->get_key() );
		$this->assertEquals( 'MslsSqlCacherTest_Cache_Test', $obj->get_key() );

		$sql = $obj->prepare(
			"SELECT blog_id FROM {$obj->blogs} WHERE blog_id != %d AND site_id = %d",
			$obj->blogid,
			$obj->siteid
		);
		$this->assertInternalType( 'string', $sql );
		$this->assertInternalType( 'array', $obj->get_results( $sql ) );
	}

}
