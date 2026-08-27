<?php declare( strict_types=1 );

namespace lloc\MslsTests\Frontend\Map;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Frontend\Map\HrefLang;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestHrefLang extends MslsUnitTestCase {

	private function HrefLangFactory(): HrefLang {
		$map = array(
			'de_DE'        => 'de',
			'de_DE_formal' => 'de',
			'fr_FR'        => 'fr',
			'es_ES'        => 'es',
			'cat'          => 'cat',
			'en_US'        => 'en',
			'en_GB'        => 'en',
		);

		foreach ( $map as $locale => $alpha2 ) {
			$blog = \Mockery::mock( Blog::class );
			$blog->shouldReceive( 'get_alpha2' )->andReturn( $alpha2 );
			$blog->shouldReceive( 'get_language' )->andReturn( $locale );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		return new HrefLang( $collection );
	}

	/**
	 * @return array<string, array{string, string}>
	 */
	public static function hreflang_provider(): array {
		return array(
			'de_DE'        => array( 'de_DE', 'de-DE' ),
			'de_DE_formal' => array( 'de_DE_formal', 'de-DE' ),
			'fr_FR'        => array( 'fr_FR', 'fr' ),
			'es_ES'        => array( 'es_ES', 'es' ),
			'cat'          => array( 'cat', 'cat' ),
			'en_GB'        => array( 'en_GB', 'en-GB' ),
			'en_US'        => array( 'en_US', 'en-US' ),
		);
	}

	#[DataProvider( 'hreflang_provider' )]
	public function test_get( string $locale, string $expected ): void {
		$test = $this->HrefLangFactory();

		$this->assertEquals( $expected, $test->get( $locale ) );
	}

	public function test_get_has_filter(): void {
		Functions\when( 'has_filter' )->justReturn( true );
		Filters\expectApplied( 'msls_head_hreflang' )->once()->with( 'en_US' )->andReturn( 'en-US' );

		$test = $this->HrefLangFactory();

		$this->assertEquals( 'en-US', $test->get( 'en_US' ) );
	}
}
