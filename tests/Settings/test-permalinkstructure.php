<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\Settings\PermalinkStructure;

class WP_Test_PermalinkStructure extends Msls_UnitTestCase {


	public function test_get_false() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( false );

		$url = 'http://example.org/test';

		$obj = new PermalinkStructure( 1 );

		$this->assertEquals( $url, $obj->get_home_url( $url ) );
	}

	public function test_get_true() {
		$needle = '/directory';
		$args   = $needle . '/%year%/%postname%/';

		$domain = 'http://example.org';
		$path   = '/test';

		$url  = $domain . '/' . $needle . $path;
		$expected = $domain . $path;

		Functions\expect( 'get_blog_option' )->twice()->andReturn( $args );
		Functions\expect( 'home_url' )->twice()->withNoArgs()->andReturn( $domain );
		Functions\expect( 'home_url' )->twice()->andReturnFirstArg();
		Functions\expect( 'is_main_site' )->twice()->andReturn( false );

		$obj = new PermalinkStructure( 1 );

		$this->assertEquals( $expected, $obj->get_home_url( $url, true ) );
		$this->assertEquals( $expected, $obj->get_home_url( $url, true ) );
	}

}
