<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlock;
use lloc\Msls\MslsOptions;

use lloc\Msls\MslsShortCode;

use function Brain\Monkey\Functions;

class TestMslsShortCode extends MslsUnitTestCase {

	public function test_block_render_excluded_true(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$this->assertEquals( '', ( new MslsShortCode( $options ) )->block_render() );
	}


	public function test_block_render_excluded_false(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		$expected = '<div class="msls-shortcode">Widget Output</div>';

		Functions\when( 'the_widget' )->justEcho( $expected );

		$this->assertEquals( $expected, ( new MslsShortCode( $options ) )->block_render() );
	}
}
