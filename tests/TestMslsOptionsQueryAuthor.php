<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryAuthor;

class TestMslsOptionsQueryAuthor extends MslsUnitTestCase {

	protected function setUp(): void {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->test = new MslsOptionsQueryAuthor();
	}

	public function test_has_value_method(): void {
		$this->assertIsBool( $this->test->has_value( 'de_DE' ) );
	}

	public function test_get_current_link_method(): void {
		Functions\expect( 'get_author_posts_url' )->once()->andReturn( 'https://example.org/queried-author' );

		$this->assertEquals( 'https://example.org/queried-author', $this->test->get_current_link() );
	}

}
