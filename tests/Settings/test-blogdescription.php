<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\Settings\BlogDescription;

class WP_Test_BlogDescription extends Msls_UnitTestCase {

	public function test_get_empty() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( [] );
		$obj = new BlogDescription( 1 );

		$this->assertNull( $obj->get() );
	}

	public function test_get_excluded() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( [ 'exclude_current_blog' => true ] );
		$obj = new BlogDescription( 1 );

		$this->assertNull( $obj->get() );
	}

	public function test_get() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( [ 'exclude_current_blog' => false, 'description' => 'Test' ] );
		$obj = new BlogDescription( 1 );

		$this->assertEquals( 'Test', $obj->get() );
	}

}
