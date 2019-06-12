<?php
/**
 * Tests for MslsCustomFilter
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsCustomFilter;

/**
 * WP_Test_MslsCustomFilter
 */
class WP_Test_MslsCustomFilter extends Msls_UnitTestCase {

	/**
	 * Verify the init-method
	 */
	function test_init_method() {
		$obj   = MslsCustomFilter::init();
		$query = $this->getMockBuilder( WP_Query::class )->getMock();

		$this->assertInstanceOf( MslsCustomFilter::class, $obj );
		$this->assertFalse( $obj->execute_filter( $query ) );
	}

}
