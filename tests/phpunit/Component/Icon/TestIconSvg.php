<?php declare( strict_types=1 );

namespace lloc\MslsTests\Component\Icon;

use Brain\Monkey\Functions;
use lloc\Msls\Component\Icon\IconSvg;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestIconSvg extends MslsUnitTestCase {

	/**
	 * @return array<string, array{string, string}>
	 */
	public static function locale_provider(): array {
		$locales = array(
			'cs_CZ' => 'flag-icon-cz',
			'eo'    => 'flag-icon-eu',
			'ca'    => 'flag-icon-es-ca',
			'pinko' => 'flag-icon-ko',
		);

		$data = array();

		foreach ( $locales as $locale => $expected ) {
			$data[ $locale ] = array( $locale, $expected );
		}

		return $data;
	}

	#[DataProvider( 'locale_provider' )]
	public function test_get( string $locale, string $expected ): void {
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 4 ) . '/' );

		$obj = new IconSvg();

		$this->assertEquals( $expected, $obj->get( $locale ) );
	}
}
