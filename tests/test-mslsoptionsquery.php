<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsOptionsQuery;

/**
 * WP_Test_MslsOptionsQuery
 */
class WP_Test_MslsOptionsQuery extends Msls_UnitTestCase {

	/**
	 * Verify the static create-method
	 */
	function test_create_method() {
		$this->assertNull( MslsOptionsQuery::create() );
		return new MslsOptionsQuery();
	}

	/**
	 * Verify the get_current_link-method
	 * @depends test_create_method
	 */
	function test_get_current_link_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

}
