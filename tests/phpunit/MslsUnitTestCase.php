<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class MslsUnitTestCase extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		\Mockery::namedMock( 'WooCommerce', \stdClass::class );

		Functions\when( '__' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_html__' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'wp_kses' )->returnArg();
		Functions\when( 'wp_kses_post' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'wp_kses_allowed_html' )->justReturn( array() );
	}

	protected function tearDown(): void {
		\Mockery::close();
		Monkey\tearDown();

		parent::tearDown();
	}
}
