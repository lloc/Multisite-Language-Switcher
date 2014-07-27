<?php
/**
 * Tests for MslsLanguageArray
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsLanguageArray
 */
class WP_Test_MslsLanguageArray extends Msls_UnitTestCase {

	/**
	 * Verify the get_val-method
	 * @covers MslsLanguageArray::__construct
	 * @covers MslsLanguageArray::get_val
	 */
	function test_get_val_method() {
		$arr = array(
			'fr_FR' => 0, // not ok, value 0 is not ok as blog_id
			'it'    => 1,
			'de_DE' => 2,
			'x'     => 3, // not ok, minlength of string is 2
		);
		$obj = new MslsLanguageArray( $arr );
		$this->assertEquals( 1, $obj->get_val( 'it' ) );
		$this->assertEquals( 0, $obj->get_val( 'fr_FR' ) );
		return $obj;
	}

	/**
	 * Verify the get_arr-method
	 * @depends test_get_val_method
	 */
	function test_get_arr_method( $obj ) {
		$this->assertEquals( array( 'it' => 1, 'de_DE' => 2 ), $obj->get_arr() );
		$this->assertEquals( array( 'it' => 1 ), $obj->get_arr( 'de_DE' ) );
	}

	/**
	 * Verify the set-method
	 * @depends test_get_val_method
	 */
	function test_set_method( $obj ) {
		$this->assertEquals( array( 'it' => 1, 'de_DE' => 2 ), $obj->get_arr() );
		$obj->set( 'fr_FR', 3 );
		$this->assertEquals( array( 'it' => 1, 'de_DE' => 2, 'fr_FR' => 3 ), $obj->get_arr() );
	}

}
