<?php
/**
 * Tests for MslsPostType
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsPostType
 */
class WP_Test_MslsPostType extends Msls_UnitTestCase {

	/**
	 * Verify the instance-method
	 */
	function test_instance_method() {
		$this->assertInstanceOf( 'MslsPostType', MslsPostType::instance() );
	}

}
