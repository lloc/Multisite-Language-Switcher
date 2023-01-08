<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\Settings\PermalinkStructure;

class WP_Test_PermalinkStructure extends Msls_UnitTestCase {

	const DOMAIN = 'https://www.example.org';

	public function test_get_false() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( false );

		$url = self::DOMAIN . '/test';

		$obj = new PermalinkStructure( 1 );

		$this->assertEquals( $url, $obj->get_home_url( $url ) );
	}

	public function test_get_with_args() {
		$needle = '/directory';
		$args   = $needle . '/%year%/%postname%/';

		$path = '/test';

		$url      = self::DOMAIN . $needle . $path;
		$expected = self::DOMAIN . $path;

		Functions\expect( 'get_blog_option' )->once()->andReturn( $args );
		Functions\expect( 'is_main_site' )->once()->andReturn( false );

		Functions\when( 'home_url' )->alias( function ( $value = '' ) {
			return self::DOMAIN . $value;
		} );

		$obj = new PermalinkStructure( 1 );

		$this->assertEquals( $expected, $obj->get_home_url( $url, true ) );
	}

	public function test_get_main_site() {
		$needle = '/directory';
		$args   = $needle . '/%year%/%postname%/';

		$path = '/test';

		$url = self::DOMAIN . $needle . $path;

		Functions\expect( 'get_blog_option' )->once()->andReturn( $args );
		Functions\expect( 'is_main_site' )->once()->andReturn( true );

		Functions\when( 'home_url' )->alias( function ( $value = '' ) {
			return self::DOMAIN . $value;
		} );

		$obj = new PermalinkStructure( 1 );

		$this->assertEquals( $url, $obj->get_home_url( $url, true ) );
	}

}
