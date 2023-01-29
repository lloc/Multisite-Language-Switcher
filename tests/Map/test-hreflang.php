<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;

use lloc\Msls\Map\HrefLang;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;

class WP_Test_HrefLang extends Msls_UnitTestCase {

	/**
	 * @return \string[][]
	 */
	public function data_provider(): array {
		return [
			[ 'de-DE', 'de_DE' ],
			[ 'de-DE', 'de_DE_formal' ],
			[ 'fr', 'fr_FR' ],
			[ 'es', 'es_ES' ],
			[ 'cat', 'cat' ],
			[ 'en-GB', 'en_GB' ],
			[ 'en-US', 'en_US' ],
		];
	}

	public function get_sut(): HrefLang {
		foreach ( $this->data_provider() as $codes ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( [
				'get_alpha2'   => $codes[0],
				'get_language' => $codes[1],
			] );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		return new HrefLang( $collection );
	}

	/**
 	 * @dataProvider data_provider
	 *
	 * @param string $expected
	 * @param string $language
	 *
	 * @return void
	 */
	public function test_get( string $expected, string $language ): void {
		$obj = $this->get_sut();

		$this->assertEquals( $expected, $obj->get( $language ) );
	}

	/**
	 * @dataProvider data_provider
	 *
	 * @param string $expected
	 * @param string $language
	 *
	 * @return void
	 */
	public function test_get_has_filter( string $expected, string $language ): void {
		$obj = $this->get_sut();

		Functions\when( 'has_filter' )->justReturn( true );
		Filters\expectApplied('msls_head_hreflang')->once()->with( $language )->andReturn( $expected );

		$this->assertEquals( $expected, $obj->get( $language ) );
	}

}
