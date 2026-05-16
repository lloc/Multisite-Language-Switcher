<?php declare( strict_types=1 );

namespace lloc\MslsTests\Cli;

use Brain\Monkey\Functions;
use lloc\Msls\Cli\Cli;
use lloc\MslsTests\MslsUnitTestCase;

final class TestCli extends MslsUnitTestCase {

	private function CliFactory(): Cli {
		return new Cli();
	}

	public function test_blog_success(): void {
		Functions\expect( 'msls_blog' )->once()->with( 'de_DE' )->andReturn( (object) array( 'userblog_id' => 1 ) );

		$this->expectOutputString( 'Blog ID 1 has locale de_DE!' );

		$this->CliFactory()->blog( array( 'de_DE' ), array() );
	}

	public function test_blog_error(): void {
		Functions\expect( 'msls_blog' )->once()->with( 'de_DE' )->andReturnNull();

		$this->expectOutputString( 'No blog with locale de_DE found!' );

		$this->CliFactory()->blog( array( 'de_DE' ), array() );
	}
}
