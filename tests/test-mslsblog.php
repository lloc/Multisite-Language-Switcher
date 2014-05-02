<?php
/**
 * Tests for MslsBlog
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsBlog
 */
class WP_Test_MslsBlog extends WP_UnitTestCase {

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
	 * Verify the __get-method
	 */
	function test___get_method() {
		$obj = new MslsBlog( null, null );
		$this->assertEquals( null, $obj->test_var );

		$blog = new stdClass();
		$blog->userblog_id = 1;
		$obj = new MslsBlog( $blog, null );
		$this->assertEquals( 1, $obj->userblog_id );
		$this->assertEquals( null, $obj->test_var );
	}

	/**
	 * Verify the get_description-method
	 */
	function test_get_description_method() {
		$obj = new MslsBlog( null, 'Test' );
		$this->assertEquals( 'Test', $obj->get_description() );

		$obj = new MslsBlog( null, null );
		$this->assertEquals( 'us', $obj->get_description() );
	}
	
	/**
	 * Verify the get_language-method
	 */
	function test_get_language_method() {
		$obj = new MslsBlog( null, null );
		$this->assertEquals( 'us', $obj->get_description() );
	}
	
	/**
	 * Verify the get_alpha2-method
	 */
	function test_get_alpha2_method() {
		$obj = new MslsBlog( null, null );
		$this->assertEquals( 'en', $obj->get_alpha2() );
	}

	/**
	 * Verify the _cmp-method
	 */
	function test__cmp_method() {
		$obj = new MslsBlog( null, null );
		$a = $b = 0;
		$this->assertEquals( 0, $obj->_cmp( $a, $b ) );
		$b = 1;
		$this->assertEquals( -1, $obj->_cmp( $a, $b ) );
		$a = 2;
		$this->assertEquals( 1, $obj->_cmp( $a, $b ) );
	}

	/**
	 * Verify the language-method
	 */
	function test_language_method() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->language( $a, $b ) );
	}

	/**
	 * Verify the description-method
	 */
	function test_description_method() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->language( $a, $b ) );
	}

}
