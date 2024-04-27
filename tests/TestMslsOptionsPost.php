<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsPost;

class TestMslsOptionsPost extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->test = new MslsOptionsPost();
	}

	public function test_get_postlink_not_has_value() {
		$this->assertEquals( '', $this->test->get_postlink( 'es_ES' ) );
	}

	public function test_get_postlink_post_is_null(): void {
		Functions\expect( 'get_post' )->once()->andReturnNull();

		$this->assertEquals( '', $this->test->get_postlink( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://example.org/a-post' );

		$this->assertEquals( 'https://example.org/a-post', $this->test->get_current_link() );
	}

}
