<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsOptions;

class TestMslsMain extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		$options = \Mockery::mock( MslsOptions::class );

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blog );

		$this->test = new MslsMain( $options, $collection );
	}

	public function test_get_input_array_empty(): void {
		$this->assertEquals( array( 'de_DE' => 0 ), $this->test->get_input_array( 0 ) );
	}

	public function test_get_input_array(): void {
		Functions\when( 'filter_input_array' )->justReturn(
			array(
				'some_other_key'   => 1,
				'msls_input_de_DE' => 2,
			)
		);

		$this->assertEquals( array( 'de_DE' => 2 ), $this->test->get_input_array( 1 ) );
	}

	public function test_is_autosave(): void {
		Functions\when( 'wp_is_post_revision' )->justReturn( true );

		$this->assertIsBool( $this->test->is_autosave( 0 ) );
	}

	public function test_verify_nonce(): void {
		$this->assertFalse( $this->test->verify_nonce() );
	}

	public function test_debugger_string(): void {
		$capture = tmpfile();
		$backup  = ini_set( 'error_log', stream_get_meta_data( $capture )['uri'] );

		$this->test->debugger( 'Test' );

		$this->assertStringContainsString( 'MSLS Debug: Test', stream_get_contents( $capture ) );

		ini_set( 'error_log', $backup );
	}

	public function test_debugger_object(): void {
		$capture = tmpfile();
		$backup  = ini_set( 'error_log', stream_get_meta_data( $capture )['uri'] );

		$this->test->debugger( (object) array( 'test' => 'msls' ) );

		$this->assertStringContainsString( '[test] => msls', stream_get_contents( $capture ) );

		ini_set( 'error_log', $backup );
	}
}
