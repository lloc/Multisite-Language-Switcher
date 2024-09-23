<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

class MslsUnitTestCase extends TestCase {

	/**
	 * Instance of the class to test
	 *
	 * @var object $test
	 */
	protected object $test;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		\Mockery::namedMock( 'WooCommerce', \stdClass::class );

		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( '__' )->returnArg();
		Functions\when( 'wp_kses' )->returnArg();
		Functions\when( 'wp_kses_post' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
	}


	protected function tearDown(): void {
		restore_error_handler();

		Monkey\tearDown();
		\Mockery::close();

		parent::tearDown();
	}
}
