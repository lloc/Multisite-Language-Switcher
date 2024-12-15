<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsShortCode;

final class TestMslsShortCode extends MslsUnitTestCase {

	public function test_init(): void {
		Functions\expect( 'add_shortcode' )->once()->with( 'sc_msls_widget', array( MslsShortCode::class, 'render_widget' ) );
		Functions\expect( 'add_shortcode' )->once()->with( 'sc_msls', 'get_the_msls' );

		$this->expectNotToPerformAssertions();
		MslsShortCode::init();
	}

	public function test_block_render_excluded_true(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );

		$this->assertEquals( '', MslsShortCode::render_widget() );
	}


	public function test_block_render_excluded_false(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );

		$expected = '<div class="msls-shortcode">Widget Output</div>';

		Functions\when( 'the_widget' )->justEcho( $expected );

		$this->assertEquals( $expected, MslsShortCode::render_widget() );
	}
}
