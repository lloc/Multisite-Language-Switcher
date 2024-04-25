<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsLanguageArray;

class TestMslsLanguageArray extends Msls_UnitTestCase {

	public function get_test() {
		$arr = array(
			'fr_FR' => 0, // not ok, value 0 is not ok as blog_id
			'it'    => 1,
			'de_DE' => 2,
			'x'     => 3, // not ok, minlength of string is 2
		);

		return new MslsLanguageArray( $arr );
	}

	function test_get_val() {
		$obj = $this->get_test();

		$this->assertEquals( 1, $obj->get_val( 'it' ) );
		$this->assertEquals( 0, $obj->get_val( 'fr_FR' ) );

		return $obj;
	}

	function test_get_arr() {
		$obj = $this->get_test();

		$this->assertEquals( [ 'it' => 1, 'de_DE' => 2 ], $obj->get_arr() );
		$this->assertEquals( [ 'it' => 1 ], $obj->get_arr( 'de_DE' ) );
	}

	function test_set() {
		$obj = $this->get_test();

		$this->assertEquals( [ 'it' => 1, 'de_DE' => 2 ], $obj->get_arr() );
		$obj->set( 'fr_FR', 3 );
		$this->assertEquals( [ 'it' => 1, 'de_DE' => 2, 'fr_FR' => 3 ], $obj->get_arr() );
	}

}
