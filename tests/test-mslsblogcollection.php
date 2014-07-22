<?php
/**
 * Tests for MslsBlogCollection
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsBlogCollection
 */
class WP_Test_MslsBlogCollection extends WP_UnitTestCase {

	/**
	 * SetUp initial settings
	 */
	function setUp() {
		parent::setUp();
		wp_cache_flush();
	}

	/**
	 * Break down for next test
	 */
	function tearDown() {
		parent::tearDown();
	}

	/**
	 * Verify the instance-method
	 */
	function test_instance_method() {
		$obj =  MslsBlogCollection::instance();
		$this->assertInstanceOf( 'MslsBlogCollection', $obj );
		return $obj;
	}

	/**
	 * Verify the get_configured_blog_description-method
	 * @depends test_instance_method
	 */
	function test_get_description_method( $obj ) {
		$this->assertEquals( 'Test', $obj->get_configured_blog_description( 0, 'Test' ) );
		$this->assertEquals( false, $obj->get_configured_blog_description( 0, false ) );
	}

	/**
	 * Verify the test_get_blogs_of_reference_user-method
	 * @depends test_instance_method
	 */
	function test_get_blogs_of_reference_user( $obj ) {
		$option = $this->getMock( 'MslsOptions' );
		$this->assertInternalType( 'array', $obj->get_blogs_of_reference_user( $options ) );
	}

}
