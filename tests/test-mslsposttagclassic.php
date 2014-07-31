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
		$obj =  MslsPostTagClassic::init();
		$this->assertInstanceOf( 'MslsPostTagClassic', $obj );
		return $obj;
	}

	/**
	 * Verify the static the_input-method
	 * @depends test_init_method
	 */
	function test_the_input_method( $obj ) {
		$tag = new StdClass;
		$tag->term_id = 1;
		$this->assertInternalType( 'boolean', $obj->the_input( $tag, 'test', 'test' ) );
	}

}
