<?php
/**
 * Tests for MslsCustomColumn
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsCustomColumn;

/**
 * WP_Test_MslsCustomColumn
 */
class WP_Test_MslsCustomColumn extends Msls_UnitTestCase {

	/**
	 * Verify the init-method
	 */
	function test_init_method() {
		$obj = MslsCustomColumn::init();
		$this->assertInstanceOf( MslsCustomColumn::class, $obj );

		$this->assertInternalType( 'array', $obj->th( array() ) );
	}

}
