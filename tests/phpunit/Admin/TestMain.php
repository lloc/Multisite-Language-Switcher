<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin;

use Brain\Monkey\Functions;
use lloc\Msls\Admin\Main;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestMain extends MslsUnitTestCase {

	private function MslsMainFactory(): Main {
		$options = \Mockery::mock( Options::class );

		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blog );

		return new Main( $options, $collection );
	}

	public function test_get_input_array_empty(): void {
		$test = $this->MslsMainFactory();

		$this->assertEquals( array( 'de_DE' => 0 ), $test->get_input_array( 0 ) );
	}

	public function test_get_input_array(): void {
		Functions\when( 'filter_input_array' )->justReturn(
			array(
				'some_other_key'   => 1,
				'msls_input_de_DE' => 2,
			)
		);

		$test = $this->MslsMainFactory();

		$this->assertEquals( array( 'de_DE' => 2 ), $test->get_input_array( 1 ) );
	}

	public function test_is_autosave(): void {
		Functions\when( 'wp_is_post_revision' )->justReturn( true );

		$test = $this->MslsMainFactory();

		$this->assertIsBool( $test->is_autosave( 0 ) );
	}

	public function test_verify_nonce(): void {
		$test = $this->MslsMainFactory();

		$this->assertFalse( $test->verify_nonce() );
	}

	public function test_debugger_string(): void {
		$capture = tmpfile();
		$backup  = ini_set( 'error_log', stream_get_meta_data( $capture )['uri'] );

		$test = $this->MslsMainFactory();

		$test->debugger( 'Test' );

		$this->assertStringContainsString( 'MSLS Debug: Test', stream_get_contents( $capture ) );

		ini_set( 'error_log', $backup );
	}

	public function test_debugger_object(): void {
		$capture = tmpfile();
		$backup  = ini_set( 'error_log', stream_get_meta_data( $capture )['uri'] );

		$test = $this->MslsMainFactory();

		$test->debugger( (object) array( 'test' => 'msls' ) );

		$this->assertStringContainsString( '[test] => msls', stream_get_contents( $capture ) );

		ini_set( 'error_log', $backup );
	}
}
