<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsOptionsPost;

class WP_Test_MslsOptionsPost extends Msls_UnitTestCase {

	function test_get_postlink_method() {
		$obj = new MslsOptionsPost();
		$this->assertInternalType( 'string', $obj->get_postlink( 'de_DE' ) );
		return $obj;
	}

	function test_get_current_link_method( $obj ) {
		$this->assertInternalType( 'string', $obj->get_current_link() );
	}

}
