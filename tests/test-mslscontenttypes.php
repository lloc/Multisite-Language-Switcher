<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsContentTypes;

class WP_Test_MslsContentTypes extends Msls_UnitTestCase {

	public function test_is_post_type() {
		$obj = new MslsContentTypes();

		$this->assertEquals( false, $obj->is_post_type() );
	}

	public function test_is_taxonomy() {
		$obj = new MslsContentTypes();

		$this->assertEquals( false, $obj->is_taxonomy() );
	}

	public function test_acl_request() {
		$obj = new MslsContentTypes();

		$this->assertEquals( '', $obj->acl_request() );
	}

	public function test_get() {
		$obj = new MslsContentTypes();

		$this->assertEquals( [], $obj->get() );
	}

	public function test_get_request() {
		$obj = new MslsContentTypes();

		$this->assertEquals( '', $obj->get_request() );
	}

}
