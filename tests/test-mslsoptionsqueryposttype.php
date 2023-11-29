<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryPostType;

/**
 * WP_Test_MslsOptionsQueryPostType
 */
class WP_Test_MslsOptionsQueryPostType extends Msls_UnitTestCase {

	function get_test() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		return new MslsOptionsQueryPostType();
	}

	function test_has_value_method() {
		$obj = $this->get_test();

		$this->assertIsBool( $obj->has_value( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		Functions\expect( 'get_post_type_archive_link' )->once()->andReturn( 'https://example.org/queried-posttype' );

		$obj = $this->get_test();

		$this->assertEquals( 'https://example.org/queried-posttype', $obj->get_current_link() );
	}

}
