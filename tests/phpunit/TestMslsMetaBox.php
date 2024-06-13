<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsFields;
use lloc\Msls\MslsJson;
use lloc\Msls\MslsMetaBox;
use lloc\Msls\MslsOptions;

class TestMslsMetaBox extends MslsUnitTestCase {

	protected function setUp(): void {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		$this->test = new MslsMetaBox( $options, $collection );
	}

	public function test_suggest(): void {
		$json = '{"some":"JSON"}';

		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 42;

		Functions\expect( 'filter_has_var' )->times( 3 )->andReturnTrue();
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_BLOG_ID, FILTER_SANITIZE_NUMBER_INT )->andReturn( 17 );
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_POST_TYPE, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 17 );
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_S, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 17 );
		Functions\expect( 'get_post_stati' )->once()->andReturn( array( 'pending', 'draft', 'future' ) );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		Functions\expect( 'sanitize_text_field' )->times( 2 )->andReturnFirstArg();
		Functions\expect( 'get_posts' )->once()->andReturn( array( $post ) );

		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'wp_reset_postdata' )->once();

		Functions\when( 'wp_die' )->justEcho( $json );

		$this->expectOutputString( '{"some":"JSON"}' );

		MslsMetaBox::suggest();
	}

	public function test_get_suggested_fields_no_posts(): void {
		Functions\expect( 'wp_reset_postdata' )->once();
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

	public function test_render_options() {
		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 42;

		Functions\expect( 'get_posts' )->once()->andReturn( array( $post ) );
		Functions\expect( 'get_post_stati' )->once()->andReturn( array( 'pending', 'draft', 'future' ) );
		Functions\expect( 'selected' )->once()->andReturn( 'selected="selected"' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'A random title' );

		$this->assertEquals( '<option value="42" selected="selected">A random title</option>', $this->test->render_options( 'post', 42 ) );
	}
}
