<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use lloc\Msls\ServiceContainer;

final class TestServiceContainer extends MslsUnitTestCase {

	public function test_get_resolves_a_closure_with_the_container(): void {
		$test = new ServiceContainer(
			array(
				'answer'  => 42,
				'derived' => function ( ServiceContainer $container ): int {
					return $container->get( 'answer' ) + 1;
				},
			)
		);

		$this->assertEquals( 43, $test->get( 'derived' ) );
	}

	public function test_get_returns_a_value_definition_as_it_is(): void {
		$test = new ServiceContainer( array( 'flag' => false ) );

		$this->assertFalse( $test->get( 'flag' ) );
	}

	public function test_get_keeps_the_resolved_entry(): void {
		$test = new ServiceContainer(
			array(
				'object' => function (): \stdClass {
					return new \stdClass();
				},
			)
		);

		$this->assertSame( $test->get( 'object' ), $test->get( 'object' ) );
	}

	public function test_get_builds_an_undefined_class(): void {
		$test = new ServiceContainer();

		$this->assertInstanceOf( \stdClass::class, $test->get( \stdClass::class ) );
	}

	public function test_get_throws_for_an_unknown_id(): void {
		$test = new ServiceContainer();

		$this->expectException( \RuntimeException::class );

		$test->get( 'lloc\Msls\ThereIsNoSuchClass' );
	}

	public function test_has(): void {
		$test = new ServiceContainer( array( 'flag' => false ) );

		$this->assertTrue( $test->has( 'flag' ) );
		$this->assertTrue( $test->has( \stdClass::class ) );
		$this->assertFalse( $test->has( 'lloc\Msls\ThereIsNoSuchClass' ) );
	}
}
