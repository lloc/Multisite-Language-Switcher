<?php
/**
 * Tests for MslsPostTag
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsPostTag
 */
class WP_Test_MslsPostTag extends Msls_UnitTestCase {

	/**
	 * Verify the static suggest-method
	 */
	function test_suggest_method() {
		$this->expectOutputString( '[]' );
		MslsPostTag::suggest();
	}

	/**
	 * Verify the static init-method
	 */
	function test_init_method() {
		$this->assertInstanceOf( 'MslsPostTag', MslsPostTag::init() );
	}

}
