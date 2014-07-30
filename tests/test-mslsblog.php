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
class WP_Test_MslsBlog extends Msls_UnitTestCase {

	/**
	 * Verify the __get-method
	 * @covers MslsBlog::__construct
	 * @covers MslsBlog::__get
	 */
	function test___get_method() {
		$blog = new stdClass();
		$blog->userblog_id = 1;

		$obj = new MslsBlog( $blog, 'Test' );
		$this->assertEquals( 1, $obj->userblog_id );

		return $obj;
	}

	/**
	 * Verify the get_description-method
     * @depends test___get_method
	 */
	function test_get_description_method( $obj ) {
		$this->assertEquals( 'Test', $obj->get_description() );
	}

	/**
	 * Verify the get_language-method
	 * @depends test___get_method
	 */
	function test_get_language_method( $obj ) {
		$this->assertEquals( 'us', $obj->get_language() );
	}

	/**
	 * Verify the get_alpha2-method
     * @depends test___get_method
	 */
	function test_get_alpha2_method( $obj ) {
		$this->assertEquals( 'en', $obj->get_alpha2() );
	}

	/**
	 * Dataprovider
	 * @return multitype:multitype:number
	 */
	public function compareProvider() {
		return array(
			array( 0, 0, 0 ),
			array( 0, 1, -1 ),
			array( 1, 0, 1 ),
			array( -1, -2, 1 ),
			array( -2, -1, -1 )
		);
	}

	/**
	 * Verify the _cmp-method
     * @dataProvider compareProvider
     */
	function test__cmp_method( $a, $b, $expected ) {
		$this->assertEquals( $expected, MslsBlog::_cmp( $a, $b ) );
		$obj = new MslsBlog( null, null );
		$this->assertEquals( $expected, $obj->_cmp( $a, $b ) );
	}

	/**
	 * Verify the language-method
	 */
	function test_language_cmp() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->language( $a, $b ) );
	}

	/**
	 * Verify the description-method
	 */
	function test_description_cmp() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->description( $a, $b ) );
	}

}
