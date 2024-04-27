<?php declare( strict_types=1 );

namespace lloc\MslsTests\Map;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;

use lloc\MslsTests\MslsUnitTestCase;
use lloc\Msls\Map\HrefLang;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;

class TestHrefLang extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		$map = [
			'de_DE'        => 'de',
			'de_DE_formal' => 'de',
			'fr_FR'        => 'fr',
			'es_ES'        => 'es',
			'cat'          => 'cat',
			'en_US'        => 'en',
			'en_GB'        => 'en',
		];

		foreach ( $map as $locale => $alpha2 ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( [
				'get_alpha2'   => $alpha2,
				'get_language' => $locale,
			] );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		$this->test = new HrefLang( $collection );
	}

	public function test_get(): void {
		$this->assertEquals( 'de-DE', $this->test->get( 'de_DE' ) );
		$this->assertEquals( 'de-DE', $this->test->get( 'de_DE_formal' ) );
		$this->assertEquals( 'fr', $this->test->get( 'fr_FR' ) );
		$this->assertEquals( 'es', $this->test->get( 'es_ES' ) );
		$this->assertEquals( 'cat', $this->test->get( 'cat' ) );
		$this->assertEquals( 'en-GB', $this->test->get( 'en_GB' ) );
		$this->assertEquals( 'en-US', $this->test->get( 'en_US' ) );
	}

	public function test_get_has_filter(): void {
		Functions\when( 'has_filter' )->justReturn( true );
		Filters\expectApplied( 'msls_head_hreflang' )->once()->with( 'en_US' )->andReturn( 'en-US' );

		$this->assertEquals( 'en-US', $this->test->get( 'en_US' ) );
	}

}
