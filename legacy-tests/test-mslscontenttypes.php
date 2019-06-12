<?php
/**
 * Tests for MslsContentTypes
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsContentTypes;

/**
 * WP_Test_MslsContentTypes
 */
class WP_Test_MslsContentTypes extends Msls_UnitTestCase {

	/**
	 * Verify the create-method
	 */
	function test_create_method() {
		$obj = MslsContentTypes::create();
		$this->assertInstanceOf( MslsContentTypes::class, $obj );

		$this->assertInternalType( 'boolean', $obj->is_post_type() );

		$this->assertInternalType( 'boolean', $obj->is_taxonomy() );

		$this->assertInternalType( 'string', $obj->acl_request() );

		$this->assertInternalType( 'array', $obj->get() );

		$this->assertInternalType( 'string', $obj->get_request() );
	}

}
