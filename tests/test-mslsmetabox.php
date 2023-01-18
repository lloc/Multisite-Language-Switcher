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

	public function setUp(): void {
		parent::setUp();

		Functions\when( 'wp_die' )->echoArg();
	}

	public function get_sut(): MslsMetaBox {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		return new MslsMetaBox( $options, $collection );
	}

	public function test_suggest_no_input(): void {
		$this->expectOutputString( '[]' );

		MslsMetaBox::suggest();
	}

	public function test_render_option_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( 'selected="selected"' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$obj = $this->get_sut();

		$this->assertEquals( '<option value="1" selected="selected">Test</option>', $obj->render_option( 1, 1 ) );
	}

	public function test_render_option_not_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( '' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$obj = $this->get_sut();

		$this->assertEquals( '<option value="1" >Test</option>', $obj->render_option( 1, 2 ) );
	}

}
