<?php
/**
 * Tests for MslsJson
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsJson
 */
class WP_Test_MslsJson extends Msls_UnitTestCase {

	/**
	 * Verify the add- and the get-methods
	 * @covers MslsJson::add
	 * @covers MslsJson::get
	 * @covers MslsJson::compare
	 */
	function test_add_get_methods() {
		$obj = new MslsJson();
		$obj->add( null, 'Test 3' )
			->add( '2', 'Test 2' )
			->add( 1, 'Test 1' );
		$this->assertEquals(
			array(
				array( 'value' => 1, 'label' => 'Test 1' ),
				array( 'value' => 2, 'label' => 'Test 2' ),
				array( 'value' => 0, 'label' => 'Test 3' ),
			),
			$obj->get()
		);
		return $obj;
	}

	/**
	 * Verify the encode and the __toString-method
	 * @depends test_add_get_methods
	 */
	function test___toString_methods( $obj ) {
		$string = '[{"value":1,"label":"Test 1"},{"value":2,"label":"Test 2"},{"value":0,"label":"Test 3"}]';
		$this->assertEquals( $string, $obj->encode() );
		$this->assertEquals( $string, $obj->__toString() );
	}

}
