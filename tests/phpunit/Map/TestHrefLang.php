<?php declare( strict_types=1 );

namespace lloc\MslsTests\Map;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Map\HrefLang;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\MslsTests\MslsUnitTestCase;

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
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( 'get_alpha2' )->andReturn( $alpha2 );
			$blog->shouldReceive( 'get_language' )->andReturn( $locale );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		return new HrefLang( $collection );
	}

	public function test_get(): void {
		$test = $this->HrefLangFactory();

		$this->assertEquals( 'de-DE', $test->get( 'de_DE' ) );
		$this->assertEquals( 'de-DE', $test->get( 'de_DE_formal' ) );
		$this->assertEquals( 'fr', $test->get( 'fr_FR' ) );
		$this->assertEquals( 'es', $test->get( 'es_ES' ) );
		$this->assertEquals( 'cat', $test->get( 'cat' ) );
		$this->assertEquals( 'en-GB', $test->get( 'en_GB' ) );
		$this->assertEquals( 'en-US', $test->get( 'en_US' ) );
	}

	public function test_get_has_filter(): void {
		Functions\when( 'has_filter' )->justReturn( true );
		Filters\expectApplied( 'msls_head_hreflang' )->once()->with( 'en_US' )->andReturn( 'en-US' );

		$test = $this->HrefLangFactory();

		$this->assertEquals( 'en-US', $test->get( 'en_US' ) );
	}
}
