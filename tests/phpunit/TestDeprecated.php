<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

final class TestDeprecated extends MslsUnitTestCase {

	public function test_get_the_msls(): void {
		Functions\expect( '_deprecated_function' )->once();
		Functions\expect( 'msls_get_switcher' )->once()->andReturn( '' );

		get_the_msls( null );

		$this->expectNotToPerformAssertions();
	}

	public function test_the_msls(): void {
		Functions\expect( '_deprecated_function' )->once();
		Functions\expect( 'msls_the_switcher' )->once()->andReturn( '' );

		the_msls();

		$this->expectNotToPerformAssertions();
	}

	public function test_get_msls_flag_url(): void {
		Functions\expect( '_deprecated_function' )->once();
		Functions\expect( 'msls_get_flag_url' )->once()->andReturn( '' );

		get_msls_flag_url( 'en' );

		$this->expectNotToPerformAssertions();
	}

	public function test_get_msls_blog_description(): void {
		Functions\expect( '_deprecated_function' )->once();
		Functions\expect( 'msls_get_blog_description' )->once()->andReturn( '' );

		get_msls_blog_description( 'en' );

		$this->expectNotToPerformAssertions();
	}

	public function test_get_msls_permalink(): void {
		Functions\expect( '_deprecated_function' )->once();
		Functions\expect( 'msls_get_permalink' )->once()->andReturn( '' );

		get_msls_permalink( 'en' );

		$this->expectNotToPerformAssertions();
	}
}
