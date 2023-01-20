<?php

namespace lloc\MslsTests;

use lloc\Msls\Component\InputInterface;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;
use function Patchwork\always;
use function Patchwork\redefine;

class WP_Test_MslsMain extends Msls_UnitTestCase {

	public function get_sut(): MslsMain {
		Functions\when( 'get_options' )->justReturn( [] );

		$options    = \Mockery::mock( MslsOptions::class );

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blog );

		return new MslsMain( $options, $collection );
	}

	public function test_get_input_valid(): void {
		redefine( 'filter_input_array', always( [ 'exists' => true, 'msls_input_de_DE' => 42, 'msls_input_en_US' => 11, ] ) );

		$this->assertEquals( [ 'de_DE' => 42, 'en_US' => 11 ], $this->get_sut()->get_input_array( 0 ) );
	}

	public function test_get_input_false(): void {
		redefine( 'filter_input_array', always( false ) );

		$this->assertEquals( [ 'de_DE' => 88 ], $this->get_sut()->get_input_array( 88 ) );
	}

	public function test_is_autosave(): void {
		Functions\when( 'wp_is_post_revision' )->justReturn( true );

		$this->assertIsBool( $this->get_sut()->is_autosave( 0 ) );
	}

	public function test_verify_nonce(): void {
		$this->assertFalse( $this->get_sut()->verify_nonce() );
	}

	public function test_debugger(): void {
		$this->assertFalse( $this->get_sut()->debugger( 'Test' ) );
	}

	public function error_log_data_provider() {
		return [
			[ 'MSLS Debug: Test', 'Test' ],
			[ 'MSLS Debug: Test', [ 'Test' ] ],
			[ 'MSLS Debug: Test', (object)[ 'Test' ] ],
		];
	}

	/**
	 * @dataProvider error_log_data_provider
	 */
	public function test_error_log( string $expected, $input ): void {
		$capture = tmpfile();
		$backup  = ini_set('error_log', stream_get_meta_data( $capture )['uri']);

		$this->assertTrue( $this->get_sut()->error_log( $input ) );

		ini_set('error_log', $backup);
	}

}
