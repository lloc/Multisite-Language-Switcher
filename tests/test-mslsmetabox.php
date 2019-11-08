<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsMetaBox;
use lloc\Msls\MslsOptions;
use Brain\Monkey\Functions;

/**
 * WP_Test_MslsMetaBox
 */
class WP_Test_MslsMetaBox extends Msls_UnitTestCase {

	function get_test() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		return new MslsMetaBox( $options, $collection );
	}

	function test_render_option_selected() {
		Functions\expect( 'selected' )->once()->andReturn( 'selected="selected"' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$obj = $this->get_test();

		$this->assertEquals( '<option value="1" selected="selected">Test</option>', $obj->render_option( 1, 1 ) );
	}

	function test_render_option_not_selected() {
		Functions\expect( 'selected' )->once()->andReturn( '' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$obj = $this->get_test();

		$this->assertEquals( '<option value="1" >Test</option>', $obj->render_option( 1, 2 ) );
	}

}
