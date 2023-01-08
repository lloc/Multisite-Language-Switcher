<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsOptions;
use Brain\Monkey\Functions;
use Mockery;
use stdClass;

class WP_Test_MslsBlog extends Msls_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
	}

	public function test___get() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'it_IT' );

		$blog = new stdClass();
		$blog->userblog_id = 1;
		$blog->blogname    = 'Test';

		$obj = new MslsBlog( $blog, 'Italiano' );

		$this->assertEquals( 1, $obj->userblog_id );
		$this->assertEquals( 'Italiano', $obj->get_description() );
		$this->assertEquals( 'it_IT', $obj->get_language() );
		$this->assertEquals( 'it', $obj->get_alpha2() );
		$this->assertEquals( 'Test (Italiano)', $obj->get_title() );
	}

	public function test_get_url_current_link() {
		$blog = new stdClass();
		$blog->userblog_id = 1;
		$blog->blogname    = 'Test';

		Functions\expect( 'get_blog_option' )->twice()->andReturn( 'it_IT' );
		Functions\expect( 'get_option' )->once()->andReturn( [ 'exists' => true ] );
		Functions\expect( 'get_users' )->once()->andReturn( [ 1 ] );
		Functions\expect( 'get_blogs_of_user' )->once()->andReturn( [ $blog ] );

		$obj = new MslsBlog( $blog, 'Italiano' );

		$options = Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_current_link' )->once()->andReturn( '/current_link' );

		$this->assertEquals( '/current_link', $obj->get_url( $options ) );
	}

	public function test_get_url_get_parmalink_is_frontpage() {
		$blog = new stdClass();
		$blog->userblog_id = 2;
		$blog->blogname    = 'Test';

		Functions\expect( 'get_blog_option' )->once()->andReturn( 'it_IT' );
		Functions\expect( 'is_front_page' )->once()->andReturn( true );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$obj = new MslsBlog( $blog, 'Italiano' );

		$options = Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_permalink' )->once()->andReturn( '/permalink' );

		$this->assertEquals( '/permalink', $obj->get_url( $options ) );
	}

	/**
	 * @return array<array<int>>
	 */
	public function compareProvider() {
		return [
			[ 0, 0, 0 ],
			[ 0, 1, -1 ],
			[ 1, 0, 1 ],
			[ -1, -2, 1 ],
			[ -2, -1, -1 ],
		];
	}

	/**
	 * @dataProvider compareProvider
     */
	public function test__cmp( $a, $b, $expected ) {
		$this->assertEquals( $expected, MslsBlog::_cmp( $a, $b ) );
		$obj = new MslsBlog( null, null );
		$this->assertEquals( $expected, $obj->_cmp( $a, $b ) );
	}

	public function test_language_cmp() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->language( $a, $b ) );
	}

	public function test_description_cmp() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->description( $a, $b ) );
	}

}
