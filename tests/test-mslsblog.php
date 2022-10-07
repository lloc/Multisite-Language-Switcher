<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use Brain\Monkey\Functions;
use lloc\Msls\MslsOptions;

class WP_Test_MslsBlog extends Msls_UnitTestCase {

	/**
	 * Verify the __get-method
	 */
	function test___get() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'it_IT' );
		$blog = [ 'userblog_id' => 1, 'blogname' => 'Test' ];

		$obj = new MslsBlog( (object) $blog, 'Italiano' );

		$this->assertEquals( 1, $obj->userblog_id );
		$this->assertEquals( 'Italiano', $obj->get_description() );
		$this->assertEquals( 'it_IT', $obj->get_language() );
		$this->assertEquals( 'it', $obj->get_alpha2() );
		$this->assertEquals( 'Test (Italiano)', $obj->get_title() );

	}

	public function test_get_url_current() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'it_IT' );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_option' )->once()->andReturn( [] );
		Functions\expect( 'get_blogs_of_user' )->once()->andReturn( [] );

		$users = [
			(object) [
				'ID'            => 1,
				'user_nicename' => 'realloc'
			]
		];

		Functions\expect( 'get_users' )->once()->andReturns( $users );

		$blog = [ 'userblog_id' => 1, 'blogname' => 'Test' ];

		$obj = new MslsBlog( (object) $blog, 'Italiano' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_current_link' )->once()->andReturn( 'GET_CURRENT_LINK_USED' );
		$options->userblog_id = 1;

		$this->assertEquals( 'GET_CURRENT_LINK_USED', $obj->get_url( $options ) );
	}

	public function test_get_url() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'it_IT' );
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();

		$blog = [ 'userblog_id' => 2, 'blogname' => 'Test' ];

		$obj = new MslsBlog( (object) $blog, 'Italiano' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'has_value' )->once()->andReturn( true );
		$options->shouldReceive( 'get_permalink' )->once()->andReturn( 'GET_PERMALINK_USED' );
		$options->userblog_id = 1;

		$this->assertEquals( 'GET_PERMALINK_USED', $obj->get_url( $options ) );
	}

	/**
	 * Dataprovider
	 *
	 * @return array[]
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
	 * Verify the _cmp-method
     * @dataProvider compareProvider
     */
	function test__cmp_method( $a, $b, $expected ) {
		$this->assertEquals( $expected, MslsBlog::_cmp( $a, $b ) );
		$obj = new MslsBlog( null, null );
		$this->assertEquals( $expected, $obj->_cmp( $a, $b ) );
	}

	/**
	 * Verify the language-method
	 */
	function test_language_cmp() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->language( $a, $b ) );
	}

	/**
	 * Verify the description-method
	 */
	function test_description_cmp() {
		$a = new MslsBlog( null, null );
		$b = new MslsBlog( null, null );
		$this->assertEquals( 0, $a->description( $a, $b ) );
	}



}
