<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryAuthor;

class TestMslsOptionsQueryAuthor extends MslsUnitTestCase {

	protected function setUp(): void {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->test = new MslsOptionsQueryAuthor();
	}

	function test_has_value_method() {
		$this->assertIsBool( $this->test->has_value( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		Functions\expect( 'get_author_posts_url' )->once()->andReturn( 'https://example.org/queried-author' );

		$this->assertEquals( 'https://example.org/queried-author', $this->test->get_current_link() );
	}

}
