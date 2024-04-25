<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsJson;

class TestMslsJson extends MslsUnitTestCase {

	public function test_get(): void {
		$obj = ( new MslsJson() )->add( null, 'Test 3' )->add( '2', 'Test 2' )->add( 1, 'Test 1' );

		$expected = [
			[ 'value' => 1, 'label' => 'Test 1' ],
			[ 'value' => 2, 'label' => 'Test 2' ],
			[ 'value' => 0, 'label' => 'Test 3' ],
		];

		$this->assertEquals( $expected, $obj->get() );
	}

	public function test___toString(): void {
		$obj = ( new MslsJson() )->add( null, 'Test 3' )->add( '2', 'Test 2' )->add( 1, 'Test 1' );

		$expected = '[{"value":1,"label":"Test 1"},{"value":2,"label":"Test 2"},{"value":0,"label":"Test 3"}]';

		$this->assertEquals( $expected, $obj->__toString() );
	}

}
