<?php
/**
 * Tests for MslsGetSet
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsGetSet;

/**
 * WP_Test_MslsGetSet
 */
class WP_Test_MslsGetSet extends Msls_UnitTestCase {

	/**
	 * Verify the is_empty-method
	 */
	function test_is_empty_method() {
		$obj = new MslsGetSet();

		$this->assertTrue( $obj->is_empty() );
		$obj->abc = 'test';

		$this->assertEquals( 'test' , $obj->abc );
		$this->assertTrue( isset( $obj->abc ) );
		$this->assertFalse( $obj->is_empty() );

		unset( $obj->abc );
		$this->assertTrue( $obj->is_empty() );

		$obj->temp = 'test';

		$this->assertTrue( $obj->has_value( 'temp' ) );

		$this->assertEquals( array( 'temp' => 'test' ), $obj->get_arr() );

		$obj->reset();
		$this->assertEquals( array(), $obj->get_arr() );
	}

}
