<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryPostType;

/**
 * WP_Test_MslsOptionsQueryPostType
 */
class WP_Test_MslsOptionsQueryPostType extends Msls_UnitTestCase {

	function get_sut() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		return new MslsOptionsQueryPostType();
	}

	function test_has_value() {
		$this->assertTrue( $this->get_sut()->has_value( 'de_DE' ) );
	}

	function test_has_no_value() {
		$post_type = \Mockery::mock( \WP_Post_Type::class );

		Functions\expect( 'get_post_type_object' )->once()->andReturn( $post_type );

		$this->assertTrue( $this->get_sut()->has_value( 'it_IT' ) );
	}

	function test_get_current_link() {
		Functions\expect( 'get_post_type_archive_link' )->once()->andReturn( 'https://example.org/queried-posttype' );

		$this->assertEquals( 'https://example.org/queried-posttype', $this->get_sut()->get_current_link() );
	}

}
