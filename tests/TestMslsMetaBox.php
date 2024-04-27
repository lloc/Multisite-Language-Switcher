<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsMetaBox;
use lloc\Msls\MslsOptions;
use Brain\Monkey\Functions;

/**
 * TestMslsMetaBox
 */
class TestMslsMetaBox extends MslsUnitTestCase {

	function get_test(): MslsMetaBox {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		return new MslsMetaBox( $options, $collection );
	}

	public function test_render_option_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( 'selected="selected"' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$obj = $this->get_test();

		$this->assertEquals( '<option value="1" selected="selected">Test</option>', $obj->render_option( 1, 1 ) );
	}

	public function test_render_option_not_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( '' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$obj = $this->get_test();

		$this->assertEquals( '<option value="1" >Test</option>', $obj->render_option( 1, 2 ) );
	}

}
