<?php
/**
 * Tests for MslsPostTagClassic
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsPostTagClassic
 */
class WP_Test_MslsPostTagClassic extends Msls_UnitTestCase {

	/**
	 * Verify the static init-method
	 */
	function test_init_method() {
		$this->assertInstanceOf( 'MslsPostTagClassic', MslsPostTagClassic::init() );
	}

}
