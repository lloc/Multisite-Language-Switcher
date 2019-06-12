<?php

namespace lloc\MslsTests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;

class Msls_UnitTestCase extends TestCase {

	protected function setUp() {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}
}