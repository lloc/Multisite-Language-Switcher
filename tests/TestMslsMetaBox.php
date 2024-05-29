<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsJson;
use lloc\Msls\MslsMetaBox;
use lloc\Msls\MslsOptions;
use Brain\Monkey\Functions;

/**
 * TestMslsMetaBox
 */
class TestMslsMetaBox extends MslsUnitTestCase {

	protected function setUp(): void {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		$this->test = new MslsMetaBox( $options, $collection );
	}

	public function test_suggest(): void {
		$json = json_encode( array( 'some' => 'JSON' ) );

		Functions\when( 'wp_die' )->justEcho( $json );

		$this->expectOutputString( '{"some":"JSON"}' );

		MslsMetaBox::suggest();
	}

	public function test_get_suggested_fields_no_posts(): void {
		Functions\expect( 'wp_reset_postdata' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'get_posts' )->once()->andReturn( array() );

		$json = \Mockery::mock( MslsJson::class );
		$args = array();

		$this->assertEquals( $json, MslsMetaBox::get_suggested_fields( $json, $args ) );
	}

	public function test_render_option_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( 'selected="selected"' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$this->assertEquals( '<option value="1" selected="selected">Test</option>', $this->test->render_option( 1, 1 ) );
	}

	public function test_render_option_not_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( '' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$this->assertEquals( '<option value="1" >Test</option>', $this->test->render_option( 1, 2 ) );
	}
}
