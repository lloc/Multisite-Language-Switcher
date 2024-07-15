<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsPostType;

class TestMslsPostType extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'get_post_types' )->justReturn( array() );
		Functions\when( 'get_post_type' )->justReturn( array() );

		$this->test = new MslsPostType();
	}

	public function test_is_post_type(): void {
		$this->assertTrue( $this->test->is_post_type() );
	}

	public function test_acl_request(): void {
		$this->assertEquals( '', $this->test->acl_request() );
	}
}
