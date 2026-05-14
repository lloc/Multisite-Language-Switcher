<?php declare( strict_types=1 );

namespace lloc\MslsTests\ContentTypes;

use Brain\Monkey\Functions;
use lloc\Msls\ContentTypes\PostType;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestPostType extends MslsUnitTestCase {

	private function MslsPostTypeFactory(): PostType {
		Functions\when( 'get_post_types' )->justReturn( array() );
		Functions\when( 'get_post_type' )->justReturn( array() );

		return new PostType();
	}

	public function test_is_post_type(): void {
		$test = $this->MslsPostTypeFactory();

		$this->assertTrue( $test->is_post_type() );
	}

	public function test_acl_request(): void {
		$test = $this->MslsPostTypeFactory();

		$this->assertEquals( '', $test->acl_request() );
	}
}
