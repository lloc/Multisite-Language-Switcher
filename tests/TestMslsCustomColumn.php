<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;

class TestMslsCustomColumn extends MslsUnitTestCase {

	public function test_th(): void {
		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'https://example.org/added-args' );
		Functions\expect( 'get_the_ID' )->twice()->andReturnValues( [ 1, 2 ] );
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 1 ) . '/' );

		$options = \Mockery::mock( MslsOptions::class );

		foreach ( [ 'de_DE' => 'de', 'en_US' => 'en' ] as $locale => $alpha2 ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( [
				'get_alpha2'   => $alpha2,
				'get_language' => $locale,
			] );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturn( 1 );

		$obj      = new MslsCustomColumn( $options, $collection );
		$expected = [ 'mslscol' => '<span class="msls-icon-wrapper "><span class="flag-icon flag-icon-de">de_DE</span></span><span class="msls-icon-wrapper "><span class="flag-icon flag-icon-us">en_US</span></span>' ];

		$this->assertEquals( $expected, $obj->th( [] ) );
	}

	public function test_th_empty(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$obj = new MslsCustomColumn( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );
	}

}
