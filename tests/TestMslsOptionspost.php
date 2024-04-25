<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsPost;

class TestMslsOptionsPost extends Msls_UnitTestCase {

	public function get_test() {
		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		return new MslsOptionsPost();
	}

	function test_get_postlink_method() {
		Functions\expect( 'get_post' )->once()->andReturnNull();

		$obj = $this->get_test();

		$this->assertIsSTring( $obj->get_postlink( 'de_DE' ) );
	}

	function test_get_current_link_method() {
		Functions\expect( 'get_permalink' )->once()->andReturn( 'https://example.org/a-post' );

		$obj = $this->get_test();

		$this->assertEquals( 'https://example.org/a-post', $obj->get_current_link() );
	}

}
