<?php declare( strict_types=1 );

namespace lloc\MslsTests\Frontend;

use Brain\Monkey\Functions;
use lloc\Msls\Frontend\ShortCode;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;

final class TestShortCode extends MslsUnitTestCase {

	public function test_init(): void {
		Functions\expect( 'add_shortcode' )->once()->with( 'sc_msls_widget', array( ShortCode::class, 'render_widget' ) );
		Functions\expect( 'add_shortcode' )->once()->with( 'sc_msls', 'msls_get_switcher' );

		$this->expectNotToPerformAssertions();
		ShortCode::init();
	}

	public function test_block_render_excluded_true(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );

		$this->assertEquals( '', ShortCode::render_widget() );
	}


	public function test_block_render_excluded_false(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );

		$expected = '<div class="msls-shortcode">Widget Output</div>';

		Functions\when( 'the_widget' )->justEcho( $expected );

		$this->assertEquals( $expected, ShortCode::render_widget() );
	}
}
