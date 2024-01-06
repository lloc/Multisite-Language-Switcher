<?php

namespace lloc\MslsTests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class Msls_UnitTestCase extends TestCase {

	/**
	 * Instance of the class to test
	 *
	 * @var object $test
	 */
	protected object $test;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( '__' )->returnArg();
	}


	protected function tearDown(): void {
		restore_error_handler();

		Monkey\tearDown();
		parent::tearDown();
	}

}
