<?php declare( strict_types=1 );

namespace lloc\MslsTests\Component\Icon;

use Brain\Monkey\Functions;
use lloc\Msls\Component\Icon\IconPng;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestIconPng extends MslsUnitTestCase {

	/**
	 * @return array<string, array{string, string}>
	 */
	public static function locale_provider(): array {
		$locales = array(
			'cs_CZ' => 'cz.png',
			'eo'    => 'europeanunion.png',
			'ca'    => 'catalonia.png',
			'pinko' => 'ko.png',
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

		$obj = new IconPng();

		$this->assertEquals( $expected, $obj->get( $locale ) );
	}
}
