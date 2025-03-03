<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsCli;

final class TestMslsCli extends MslsUnitTestCase {

	private function MslsCliFacotry(): MslsCli {
		return new MslsCli();
	}

	public function test_blog_success(): void {
		Functions\expect( 'msls_blog' )->once()->with( 'de_DE' )->andReturn( (object) array( 'userblog_id' => 1 ) );

		$this->expectOutputString( 'Blog ID 1 has locale de_DE!' );

		$this->MslsCliFacotry()->blog( array( 'de_DE' ), array() );
	}

	public function test_blog_error(): void {
		Functions\expect( 'msls_blog' )->once()->with( 'de_DE' )->andReturnNull();

		$this->expectOutputString( 'No blog with locale de_DE found!' );

		$this->MslsCliFacotry()->blog( array( 'de_DE' ), array() );
	}
}
