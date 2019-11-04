<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use Brain\Monkey\Functions;

class WP_Test_MslsBlog extends Msls_UnitTestCase {

	/**
	 * Verify the __get-method
	 */
	function test___get_method() {
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'it_IT' );
		$blog = new \stdClass();
		$blog->userblog_id = 1;

		$obj = new MslsBlog( $blog, 'Test' );

		$this->assertEquals( 1, $obj->userblog_id );
		$this->assertEquals( 'Test', $obj->get_description() );
		$this->assertEquals( 'it_IT', $obj->get_language() );
		$this->assertEquals( 'it', $obj->get_alpha2() );
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
