<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestDeprecated extends MslsUnitTestCase {

	/**
	 * Maps every deprecated global to the arguments it is called with and the function it
	 * has to forward to.
	 *
	 * @return array<string, array{string, array<int, mixed>, string}>
	 */
	public static function deprecated_provider(): array {
		$legacy_functions = array(
			'get_the_msls'              => array( array( null ), 'msls_get_switcher' ),
			'the_msls'                  => array( array(), 'msls_the_switcher' ),
			'get_msls_flag_url'         => array( array( 'en' ), 'msls_get_flag_url' ),
			'get_msls_blog_description' => array( array( 'en' ), 'msls_get_blog_description' ),
			'get_msls_permalink'        => array( array( 'en' ), 'msls_get_permalink' ),
		);

		$data = array();

		foreach ( $legacy_functions as $legacy => list( $args, $replacement ) ) {
			$data[ $legacy ] = array( $legacy, $args, $replacement );
		}

		return $data;
	}

	/**
	 * @param array<int, mixed> $args
	 */
	#[DataProvider( 'deprecated_provider' )]
	public function test_legacy_function_warns_and_forwards( string $legacy, array $args, string $replacement ): void {
		Functions\expect( '_deprecated_function' )->once();
		Functions\expect( $replacement )->once()->andReturn( '' );

		$legacy( ...$args );

		$this->expectNotToPerformAssertions();
	}
}
