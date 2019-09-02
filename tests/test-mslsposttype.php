<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsPostType;

class WP_Test_MslsPostType extends Msls_UnitTestCase {

	function test_is_post_type() {
		Functions\when('get_post_types' )->justReturn( [] );
		Functions\when('get_post_type' )->justReturn( 'post' );

		$obj = new MslsPostType();

		$this->assertTrue( $obj->is_post_type() );
	}

}
