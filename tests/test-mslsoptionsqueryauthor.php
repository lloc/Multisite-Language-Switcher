<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryAuthor;

class WP_Test_MslsOptionsQueryAuthor extends Msls_UnitTestCase {

	function get_test() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		return new MslsOptionsQueryAuthor();
	}

	function test_has_value_method() {
		$obj = $this->get_test();

		$this->assertIsBool( $obj->has_value( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		Functions\expect( 'get_author_posts_url' )->once()->andReturn( 'https://example.org/queried-author' );

		$obj = $this->get_test();

		$this->assertEquals( 'https://example.org/queried-author', $obj->get_current_link() );

	}

}
