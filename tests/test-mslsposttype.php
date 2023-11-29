<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsPostType;

class WP_Test_MslsPostType extends Msls_UnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\when('get_post_types' )->justReturn( [] );
		Functions\when('get_post_type' )->justReturn( [] );

		$this->test = new MslsPostType();
	}

	function test_is_post_type() {
		$this->assertTrue( $this->test->is_post_type() );
	}

	function test_acl_request() {
		$this->assertEquals( '', $this->test->acl_request() );
	}

}
