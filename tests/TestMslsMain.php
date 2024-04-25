<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;

class TestMslsMain extends MslsUnitTestCase {

	public function get_sut(): MslsMain {
		Functions\when( 'get_options' )->justReturn( [] );

		$options    = \Mockery::mock( MslsOptions::class );

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blog );

		return new MslsMain( $options, $collection );
	}

	public function test_get_input_array(): void {
		$obj = $this->get_sut();

		$this->assertIsArray( $obj->get_input_array( 0 ) );
	}

	public function test_is_autosave(): void {
		Functions\when( 'wp_is_post_revision' )->justReturn( true );

		$obj = $this->get_sut();

		$this->assertIsBool( $obj->is_autosave( 0 ) );
	}

	public function test_verify_nonce(): void {
		$obj = $this->get_sut();

		$this->assertFalse( $obj->verify_nonce() );
	}

	public function test_debugger_string(): void {
		$capture = tmpfile();
		$backup = ini_set('error_log', stream_get_meta_data( $capture )['uri']);

		$obj = $this->get_sut();
		$obj->debugger( 'Test' );

		$this->assertStringContainsString( 'MSLS Debug: Test', stream_get_contents( $capture ) );

		ini_set('error_log', $backup);
	}

	public function test_debugger_object(): void {
		$capture = tmpfile();
		$backup = ini_set('error_log', stream_get_meta_data( $capture )['uri']);

		$obj = $this->get_sut();
		$obj->debugger( (object) [ 'test' => 'msls' ] );

		$this->assertStringContainsString( '[test] => msls', stream_get_contents( $capture ) );

		ini_set('error_log', $backup);
	}

}
