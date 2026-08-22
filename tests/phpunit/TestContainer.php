<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\Container;

final class TestContainer extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Container::reset();

		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 2 ) . '/' );
	}

	protected function tearDown(): void {
		Container::reset();

		parent::tearDown();
	}

	public function test_get_returns_the_same_container_twice(): void {
		$this->assertSame( Container::get(), Container::get() );
	}

	public function test_reset_drops_the_container(): void {
		$container = Container::get();

		Container::reset();

		$this->assertNotSame( $container, Container::get() );
	}
}
