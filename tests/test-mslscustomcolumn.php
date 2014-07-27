<?php
/**
 * Tests for MslsCustomColumn
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsCustomColumn
 */
class WP_Test_MslsCustomColumn extends Msls_UnitTestCase {

	/**
	 * Verify the init-method
	 */
	function test_init_method() {
		$obj = MslsCustomColumn::init();
		$this->assertInstanceOf( 'MslsCustomColumn', $obj );
		return $obj;
	}

	/**
	 * Verify the th-method
	 * @depends test_init_method
	 */
	function test_th_method( $obj ) {
		$this->assertInternalType( 'array', $obj->th( array() ) );
	}

}
