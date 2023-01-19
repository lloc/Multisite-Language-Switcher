<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryAuthor;

class WP_Test_MslsOptionsQueryAuthor extends Msls_UnitTestCase {

	public function get_sut(): MslsOptionsQueryAuthor {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		return new MslsOptionsQueryAuthor( 1 );
	}

	public function test_has_value(): void {
		// PostCounter will return 0 because WP_Query doesn't exist during tests
		$this->assertFalse( $this->get_sut()->has_value( 'de_DE' ) );
	}

	public function test_get_current_link_method(): void {
		$expected = 'https://example.org/queried-author';

		Functions\expect( 'get_author_posts_url' )->once()->andReturn( $expected );

		$this->assertEquals( $expected, $this->get_sut()->get_current_link() );
	}

}
