<?php

namespace lloc\MslsTests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class Msls_UnitTestCase extends TestCase {

	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( '__' )->returnArg();
	}

	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

}
