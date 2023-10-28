<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use Brain\Monkey\Filters;

use lloc\Msls\Map\HrefLang;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;

class WP_Test_HrefLang extends Msls_UnitTestCase {

	public function get_sut() {
		$map = [
			'de_DE'        => 'de',
			'de_DE_formal' => 'de',
			'fr_FR'        => 'fr',
			'es_ES'        => 'es',
			'cat'          => 'cat',
			'en_US'        => 'en',
			'en_GB'        => 'en',
		];

		$i = 1;
		foreach ( $map as $locale => $alpha2 ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( [
				'get_language' => $locale,
				'get_alpha2'   => $alpha2,
			] );
			$blog->userblog_id = $i++;

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );

		return new HrefLang( $collection );
	}

	public function test_get() {
		$obj = $this->get_sut();

		Functions\expect( 'has_filter' )->with( 'msls_head_hreflang' )->times( 8 )->andReturn( false );

		$this->assertEquals( 'de-DE', $obj->get( 'de_DE', 1 ) );
		$this->assertEquals( 'de-DE', $obj->get( 'de_DE_formal', 2 ) );
		$this->assertEquals( 'fr', $obj->get( 'fr_FR', 3 ) );
		$this->assertEquals( 'es', $obj->get( 'es_ES', 4 ) );
		$this->assertEquals( 'cat', $obj->get( 'cat', 5 ) );
		$this->assertEquals( 'en-US', $obj->get( 'en_US', 6 ) );
		$this->assertEquals( 'en-GB', $obj->get( 'en_GB', 7 ) );

		$this->assertEquals( 'en_US', $obj->get( 'en_US', 8 ) );
	}

	public function test_get_has_filter() {
		$obj = $this->get_sut();

		Functions\expect( 'has_filter' )->with( 'msls_head_hreflang' )->once()->andReturn( true );
		Filters\expectApplied('msls_head_hreflang')->with( 'en_US')->once()->andReturn( 'en-US' );

		$this->assertEquals( 'en-US', $obj->get( 'en_US', 8 ) );
	}

}
