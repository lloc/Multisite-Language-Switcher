<?php declare( strict_types=1 );

namespace lloc\MslsTests\Compat;

use lloc\Msls\Compat\Aliases;
use lloc\Msls\Frontend\Output;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Guards the add-on contract: every pre-3.0 name has to resolve through the autoloader
 * alone, at any time and in any plugin load order. MslsMenu and friends decide whether to
 * boot with a bare class_exists( lloc\Msls\MslsOptions::class ).
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState( false )]
final class TestAliases extends MslsUnitTestCase {

	/**
	 * @return array<string, array{string, class-string}>
	 */
	public static function alias_provider(): array {
		$data = array();

		foreach ( Aliases::MAP as $legacy => $current ) {
			$data[ $legacy ] = array( $legacy, $current );
		}

		return $data;
	}

	/**
	 * @param class-string $current
	 */
	#[DataProvider( 'alias_provider' )]
	public function test_legacy_name_resolves( string $legacy, string $current ): void {
		Aliases::register();

		$this->assertTrue(
			class_exists( $legacy ) || interface_exists( $legacy ),
			sprintf( 'The legacy name %s does not resolve any more.', $legacy )
		);

		$this->assertTrue(
			is_a( $legacy, $current, true ),
			sprintf( '%s is not an alias of %s.', $legacy, $current )
		);
	}

	/**
	 * PHP resolves the class named in a type declaration without autoloading, so every
	 * name an add-on may have put in one has to exist the moment register() returns.
	 *
	 * @param class-string $current
	 */
	#[DataProvider( 'alias_provider' )]
	public function test_legacy_name_is_created_eagerly( string $legacy, string $current ): void {
		if ( in_array( $legacy, Aliases::LAZY_ONLY, true ) ) {
			$this->markTestSkipped( sprintf( '%s never shipped before 3.0 and stays lazy.', $legacy ) );
		}

		Aliases::register();

		$this->assertTrue(
			class_exists( $legacy, false ) || interface_exists( $legacy, false ),
			sprintf( '%s has to be aliased without autoloading, not on demand.', $legacy )
		);
	}

	/**
	 * The regression test for the bug this whole compatibility layer exists for: MslsMenu
	 * declares get_msls_output(): lloc\Msls\MslsOutput and hands it what msls_output()
	 * returns, an instance of lloc\Msls\Frontend\Output.
	 */
	public function test_legacy_name_satisfies_a_return_type(): void {
		Aliases::register();

		$output = \Mockery::mock( Output::class );

		$this->assertInstanceOf( Output::class, $this->legacy_typed_output( $output ) );
	}

	public function test_unknown_name_is_left_alone(): void {
		Aliases::register();

		$this->assertFalse( class_exists( 'lloc\Msls\MslsThisNeverExisted' ) );
	}

	public function test_lazy_only_names_are_not_loaded_upfront(): void {
		Aliases::register();

		foreach ( Aliases::LAZY_ONLY as $legacy ) {
			$this->assertFalse(
				class_exists( $legacy, false ),
				sprintf( '%s must not be aliased before something asks for it.', $legacy )
			);
		}
	}

	/**
	 * @param mixed $output
	 */
	private function legacy_typed_output( $output ): \lloc\Msls\MslsOutput {
		return $output;
	}
}
